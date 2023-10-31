<?php

namespace App\Services\Client\Orders;

use App\Jobs\orders\client\ImmediatelyTowingOrderPayJob;
use App\Jobs\orders\client\ImmediatelyWorkshopOrderJob;
use App\Http\Requests\orders\WorkshopPreorderRequest;
use App\Jobs\orders\client\ImmediatelyTowingOrderJob;
use App\Jobs\orders\client\ImmediatelyWorkshopOrderPayJob;
use App\Jobs\revenue\AddRevenueJob;
use App\Jobs\wallets\WithdrawWalletJob;
use App\Models\Constant;
use App\Jobs\orders\client\ImmediatelyWorkshopPreOrderJob;
use App\Jobs\orders\client\ImmediatelyWorkshopPreOrderPayJob;
use App\Models\Preorder;
use App\Models\Towing;
use App\Models\TowingOrder;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\Workshop;
use App\Models\WorkshopOrder;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClientOrderService extends BaseService
{
    /**
     * @return Response
     */
    public function clientLatestOrders(): Response
    {
        $user = auth()->user();
        $orders = $user->getWorkshopOrders($user);
        return $this->customResponse(true, 'my latest order', $orders);
    }

    /**
     * @return Response
     */
    public function clientTowingOrders(): Response
    {
        $user = auth()->user();
        $orders = $user->getTowingOrders($user);
        return $this->customResponse(true, 'my towing order', $orders);
    }

    /**
     * @return Response
     */
    public function clientPreorders(): Response
    {
        $user = auth()->user();
        $orders = $user->getPreOrders($user);
        return $this->customResponse(true, 'my preorder', $orders);
    }

    public function getWorkshopOrder(WorkshopOrder $order): Response
    {
        $order->user;
        $order->workshop;
        return $this->customResponse(true, 'order details', $order);
    }

    public function workshopOrder(Workshop $workshop, Request $request): Response
    {
        DB::beginTransaction();
        $order = $workshop->order()->create([
            'stage' => 0,
            'user_id' => auth()->user()->id,
            'user_latitude' => request('latitude'),
            'user_longitude' => request('longitude'),
            'payment_method' => request('payment'),
            'address' => $request->get('addressDesc'),
        ]);
        $user = User::find(auth()->user()->id);
        $queue = [
            'id' => $order->id,
            'workshop_email' => $workshop->user->email,
            'user_email' => $user->email,
            'user_latitude' => request('latitude'),
            'user_longitude' => request('longitude'),
        ];
        try {
            ImmediatelyWorkshopOrderJob::dispatch($queue)->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
        return $this->customResponse(true, 'ordering success', $order);
    }

    public function payWorkshopOrder(WorkshopOrder $order): Response
    {
        DB::beginTransaction();
        $user = $order->user;
        $workshop = $order->workshop;

        if ($order->payment_method != 0) {
            if ($user->wallet->amount - $order->price < 0)
                return $this->customResponse(false, 'لا يوجد رصيد كافي في محفظتك، الرجاء شحن المحفظة');

            $preAmountUser = $user->wallet->amount;
            $preAmountWorkshop = $workshop->user->wallet->amount;

            $user->wallet->amount -= $order->price;
            $workshop->user->wallet->amount += $order->price;

            $user->wallet->save();
            $workshop->user->wallet->save();

            $newAmountUser = $user->wallet->amount;
            $newAmountWorkshop = $workshop->user->wallet->amount;

            $user->wallet->charges()->create([
                'charge' => $order->price,
                'pre_mount' => $preAmountUser,
                'new_amount' => $newAmountUser,
                'type' => 1,
            ]);

            $workshop->user->wallet->charges()->create([
                'charge' => $order->price,
                'pre_mount' => $preAmountWorkshop,
                'new_amount' => $newAmountWorkshop,
            ]);

            $response = [
                "id" => $order->id,
                "price" => $order->price,
                "client_email" => $user->email,
                "workshop_email" => $workshop->user->email
            ];
            try {
                ImmediatelyWorkshopOrderPayJob::dispatch($response)->onQueue('admin');
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->customResponse(false, 'Bad Internet', null, 504);
            }
        }

        $ratio = (float) Constant::where('key', 'profit ratio')->first()->value;
        $revenue = ($order->price * $ratio) / 100;

        try {
            AddRevenueJob::dispatch(["user_email" => $user->email, 'revenue' => $revenue])->onQueue('admin');
            WithdrawWalletJob::dispatch(["user_email" => $user->email, 'charge' => $revenue])->onQueue('main');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }

        $order->delete();
        DB::commit();
        return $this->customResponse(true, 'تم الدفع وإنهاء الطلب، شكرا لثقتكم بخدماتنا');
    }

    /********** Towing Order **********/

    public function getTowingOrder(TowingOrder $order): Response
    {
        $order->user;
        $order->towing;
        $order->towing->user;
        return $this->customResponse(true, 'order details', $order);
    }

    public function towingOrder(Towing $towing, Request $request): Response
    {
        DB::beginTransaction();
        $user = User::find(auth()->user()->id);
        $order = $towing->order()->create([
            'stage' => 0,
            'user_id' => $user->id,
            'user_latitude' => request('latitude'),
            'user_longitude' => request('longitude'),
            'payment_method' => request('payment'),
            'address' => $request->get('addressDesc'),
        ]);
        $queue = [
            'id' => $order->id,
            'towing_email' => $towing->user->email,
            'user_email' => $user->email,
            'user_latitude' => request('latitude'),
            'user_longitude' => request('longitude'),
        ];
        try {
            ImmediatelyTowingOrderJob::dispatch($queue)->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
        return $this->customResponse(true, 'ordering success', $order);
    }

    public function payTowingOrder(TowingOrder $order): Response
    {
        DB::beginTransaction();
        $user = $order->user;
        $towing = $order->towing;

        if ($order->payment_method != 0) {
            if ($user->wallet->amount - $order->price < 0)
                return $this->customResponse(false, 'لا يوجد رصيد كافي في محفظتك، الرجاء شحن المحفظة');

            $preAmountUser = $user->wallet->amount;
            $preAmountTowing = $towing->user->wallet->amount;

            $user->wallet->amount -= $order->price;
            $towing->user->wallet->amount += $order->price;

            $user->wallet->save();
            $towing->user->wallet->save();

            $newAmountUser = $user->wallet->amount;
            $newAmountTowing = $towing->user->wallet->amount;

            $user->wallet->charges()->create([
                'charge' => $order->price,
                'pre_mount' => $preAmountUser,
                'new_amount' => $newAmountUser,
                'type' => 1,
            ]);

            $towing->user->wallet->charges()->create([
                'charge' => $order->price,
                'pre_mount' => $preAmountTowing,
                'new_amount' => $newAmountTowing,
            ]);

            $user->wallet->amount -= $order->price;
            $towing->user->wallet->amount += $order->price;

            $response = [
                "id" => $order->id,
                "price" => $order->price,
                "client_email" => $user->email,
                "towing_email" => $towing->user->email
            ];
            try {
                ImmediatelyTowingOrderPayJob::dispatch($response)->onQueue('admin');
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->customResponse(false, 'Bad Internet', null, 504);
            }
        }

        $ratio = (float) Constant::where('key', 'profit ratio')->first()->value;
        $revenue = ($order->price * $ratio) / 100;

        try {
            AddRevenueJob::dispatch(["user_email" => $user->email, 'revenue' => $revenue])->onQueue('admin');
            WithdrawWalletJob::dispatch(["user_email" => $user->email, 'charge' => $revenue])->onQueue('main');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }

        $order->delete();
        DB::commit();
        return $this->customResponse(true, 'تم الدفع وإنهاء الطلب، شكرا لثقتكم بخدماتنا');
    }

    /********** PreOrder **********/

    public function getPreorder(Preorder $order): Response
    {
        $order->user;
        $order->workshop;
//        $order->towing->user;
        return $this->customResponse(true, 'order details', $order);
    }

    public function preorder(Workshop $workshop, WorkshopPreorderRequest $request): Response
    {
        DB::beginTransaction();
        $user = User::find(auth()->user()->id);
        $order = $workshop->preorder()->create([
            'description' => $request->description,
            'stage' => 0,
            'payment_method' => request('payment'),
            'address' => request('addressDesc'),
            'user_id' => $user->id,
        ]);
        $data = [
            'id' => $order->id,
            'description' => $order->description,
            'payment_method' => $order->payment_method,
            'address' => $order->address,
            'user_email' => $user->email,
            'workshop_email' => $workshop->user->email,
        ];
//        return \response($data);
        try {
            ImmediatelyWorkshopPreOrderJob::dispatch($data)->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
        return $this->customResponse(true, 'ordering success', $order);
    }

    public function payPreorder(Preorder $order): Response
    {
        DB::beginTransaction();
        $user = $order->user;
        $workshop = $order->workshop;

        if ($order->payment_method != 0) {
            if ($user->wallet->amount - $order->price < 0)
                return $this->customResponse(false, 'لا يوجد رصيد كافي في محفظتك، الرجاء شحن المحفظة');

            $preAmountUser = $user->wallet->amount;
            $preAmountWorkshop = $workshop->user->wallet->amount;

            $user->wallet->amount -= $order->price;
            $workshop->user->wallet->amount += $order->price;

            $user->wallet->save();
            $workshop->user->wallet->save();

            $newAmountUser = $user->wallet->amount;
            $newAmountWorkshop = $workshop->user->wallet->amount;

            $user->wallet->charges()->create([
                'charge' => $order->price,
                'pre_mount' => $preAmountUser,
                'new_amount' => $newAmountUser,
                'type' => 1,
            ]);

            $workshop->user->wallet->charges()->create([
                'charge' => $order->price,
                'pre_mount' => $preAmountWorkshop,
                'new_amount' => $newAmountWorkshop,
            ]);

            $response = [
                "id" => $order->id,
                "price" => $order->price,
                "client_email" => $user->email,
                "workshop_email" => $workshop->user->email
            ];
            try {
                ImmediatelyWorkshopPreOrderPayJob::dispatch($response)->onQueue('admin');
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->customResponse(false, 'Bad Internet', null, 504);
            }
        }

        $ratio = (float) Constant::where('key', 'profit ratio')->first()->value;
        $revenue = ($order->price * $ratio) / 100;

        try {
            AddRevenueJob::dispatch(["user_email" => $user->email, 'revenue' => $revenue])->onQueue('admin');
            WithdrawWalletJob::dispatch(["user_email" => $user->email, 'charge' => $revenue])->onQueue('main');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }

        $order->delete();
        DB::commit();
        return $this->customResponse(true, 'تم الدفع وإنهاء الطلب، شكرا لثقتكم بخدماتنا');
    }
}
