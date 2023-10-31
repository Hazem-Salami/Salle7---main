<?php

namespace App\Services\Order\Purchase;

use App\Http\Requests\order\purchase\SendPurchaseOrderRequest;
use App\Jobs\orders\purchase\CreatePurchaseOrderJob;
use App\Jobs\orders\purchase\PurchaseOrderChangesJob;
use App\Jobs\wallets\ChargeWalletJob;
use App\Jobs\wallets\WithdrawWalletJob;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService extends BaseService
{
    /**
     * @param SendPurchaseOrderRequest $request
     * @return Response
     */
    public function sendPurchaseOrder(SendPurchaseOrderRequest $request): Response
    {
        DB::beginTransaction();
        $user = User::find(auth()->user()->id);

        $purchaseOrders = array();
        $response = array();
        $sum = 0;

        if($request->payment_method == 1){

            foreach ($request->purchaseOrders as $purchaseOrder){
                $product = Product::find($purchaseOrder['product_id']);
                if(!($product && $product->quantity >= $purchaseOrder['quantity'])){
                    return $this->customResponse(false, ')، الرجاء المحاولة لاحقاً'.$product->name.'لا يوجد كمية كافية من المنتج (');
                }
                $sum += $product->price * $purchaseOrder['quantity'];
            }

            if (!$user->wallet)
                return $this->customResponse(false, 'لا يوجد لديك محفظة، الرجاء إنشاء محفظة');
            if ($user->wallet->amount - $sum < 0)
                return $this->customResponse(false, 'لا يوجد رصيد كافي في محفظتك، الرجاء شحن المحفظة');

        }

        foreach ($request->purchaseOrders as $purchaseOrder){

            $purchaseOrder = $user->purchaseOrders()->create([
                'storehouse_id' => $purchaseOrder['storehouse_id'],
                'product_id' => $purchaseOrder['product_id'],
                'payment_method'=> $request->payment_method,
                'quantity' => $purchaseOrder['quantity'],
            ]);

            $response [] = [
                "payment_method" => $purchaseOrder->payment_method,
                "email" => $purchaseOrder->storehouse->email,
                "product_code" => $purchaseOrder->product->product_code,
                "made" => $purchaseOrder->product->made,
                "buyer_id" => $user->id,
                "quantity" => $purchaseOrder->quantity,
            ];

            $purchaseOrders [] = $purchaseOrder;
        }

        try {

            CreatePurchaseOrderJob::dispatch($response)->onQueue('store');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();

        return $this->customResponse(true, 'تمت عملية الحصول على المنتجات بنجاح', $purchaseOrders);
    }

    /**
     * @return Response
     */
    public function getWaitingPurchaseOrders(): Response
    {
        $waitingPurchaseOrders = PurchaseOrder::where('user_id', auth()->user()->id)->where('stage', null)->paginate(\request('size'));

        return $this->customResponse(true, 'تمت الحصول على الطلبات بنجاح' ,$waitingPurchaseOrders);
    }

    /**
     * @return Response
     */
    public function getAcceptedPurchaseOrders(): Response
    {
        $acceptedPurchaseOrders = PurchaseOrder::where('user_id', auth()->user()->id)->where('stage', 0)->paginate(\request('size'));

        return $this->customResponse(true, 'تمت الحصول على الطلبات بنجاح', $acceptedPurchaseOrders);
    }

    /**
     * @return Response
     */
    public function getRejectedPurchaseOrders(): Response
    {
        $rejectedPurchaseOrders = PurchaseOrder::where('user_id', auth()->user()->id)->where('stage', 1)->paginate(\request('size'));

        return $this->customResponse(true, 'تمت الحصول على الطلبات بنجاح', $rejectedPurchaseOrders);
    }
    /**
     * @return Response
     */
    public function donePurchaseOrders(PurchaseOrder $purchaseOrder): Response
    {
        if($purchaseOrder->stage == 0) {
            $user = User::find(auth()->user()->id);
            $storehouse = $purchaseOrder->storehouse;

            $response [] = [
                "email" => $purchaseOrder->storehouse->email,
                "product_code" => $purchaseOrder->product->product_code,
                "made" => $purchaseOrder->product->made,
                "buyer_id" => $purchaseOrder->user_id,
                'type' => 0,
            ];

            if ($purchaseOrder->payment_method == 1) {

                $price = $purchaseOrder->product->price * $purchaseOrder->quantity;

                if (!$user->wallet)
                    return $this->customResponse(false, 'لا يوجد لديك محفظة، الرجاء إنشاء محفظة');
                if ($user->wallet->amount - $price < 0)
                    return $this->customResponse(false, 'لا يوجد رصيد كافي في محفظتك، الرجاء شحن المحفظة');

                try {

                    WithdrawWalletJob::dispatch(["user_email" => $user->email, 'charge' => $price])->onQueue('admin');
                    WithdrawWalletJob::dispatch(["user_email" => $user->email, 'charge' => $price])->onQueue('main');
                    ChargeWalletJob::dispatch(["user_email" => $storehouse->email, 'charge' => $price])->onQueue('admin');
                    ChargeWalletJob::dispatch(["user_email" => $storehouse->email, 'charge' => $price])->onQueue('store');

                } catch (\Exception $e) {
                    DB::rollBack();
                    return $this->customResponse(false, 'Bad Internet', null, 504);
                }
            }

            try {

                PurchaseOrderChangesJob::dispatch($response)->onQueue('store');

            } catch (\Exception $e) {
                DB::rollBack();
                return $this->customResponse(false, 'Bad Internet', null, 504);
            }
            DB::commit();

            $purchaseOrder->delete();
            return $this->customResponse(true, 'تم الدفع وإنهاء الطلب، شكرا لثقتكم بخدماتنا');
        }

        return $this->customResponse(false, 'لا يمكن التأكيد على هذا الطلب');
    }
}
