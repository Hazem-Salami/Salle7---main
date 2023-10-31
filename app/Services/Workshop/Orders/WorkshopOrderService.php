<?php

namespace App\Services\Workshop\Orders;

use App\Jobs\orders\workshop\ImmediatelyWorkshopOrderFinishJob;
use App\Jobs\orders\workshop\ImmediatelyWorkshopOrderJob;
use App\Jobs\orders\workshop\ImmediatelyWorkshopOrderMaintenanceJob;
use App\Models\Preorder;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopOrder;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class WorkshopOrderService extends BaseService
{
    public function getImmediatelyOrders(): Response
    {
        $id = User::find(auth()->user()->id)->workshop->id;
        return $this->customResponse(true,
            'orders list',
            WorkshopOrder::orderBy('created_at', 'asc')
                ->where('workshop_id', $id)->paginate(8));
    }

    public function showImmediatelyOrder(WorkshopOrder $order): Response
    {
        $order->user;
        $order->workshop;
        return $this->customResponse(true,
            'order details',
            $order);
    }

    /**
     * @param WorkshopOrder $order
     * @param-out status (0 for reject, 1 for accept)
     * @return Response
     */
    public function acceptImmediatelyOrderStartup(WorkshopOrder $order): Response
    {
        DB::beginTransaction();
        $id = $order->id;
        $acceptStatus = request('accept');

        if (request('accept') == 0) {
            $order->delete();
            $msg = "order rejecting success";
        } else {
            WorkshopOrder::where('user_id', $order->user_id)
                ->where('id', '!=', $order->id)
                ->delete();
            $order->stage = $acceptStatus;
            $order->save();
            $msg = "order accepting success";
        }

        $orderDetails = [
            'id' => $id,
            'acceptStatus' => $acceptStatus,
            'user_email' => $order->user->email
        ];
        try {
            ImmediatelyWorkshopOrderJob::dispatch($orderDetails)->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
        return $this->customResponse(true, $msg, $order);
    }

    /**
     * @param WorkshopOrder $order
     * @param-out status (0 in workshop, 1 on road)
     * @return Response
     */
    public function acceptImmediatelyOrderMaintenance(WorkshopOrder $order): Response
    {
        $path = '/QrCodes/';
        if (!\File::exists(public_path($path))) {
            \File::makeDirectory(public_path($path));
        }
        $time = Carbon::now();
        $time = $time->toDateString() . '_' . $time->hour . '_' . $time->minute . '_' . $time->second;
        $path = 'QrCodes/' . $time . '_qrcode_im.png';
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $writer->writeFile('Id: ' . $order->id, $path);
        DB::beginTransaction();
        $id = $order->id;
        $onRoad = request('onRoad');

        $order->qr_code = $path;
        $order->has_on_road = $onRoad;
        $order->stage = 2;
        $order->save();

        $orderDetails = [
            'id' => $id,
            'onRoad' => $onRoad,
        ];

        try {
            ImmediatelyWorkshopOrderMaintenanceJob::dispatch($orderDetails)->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
        return $this->customResponse(true, "order accepting maintenance success", $order);
    }

    /**
     * @param WorkshopOrder $order
     * @param-out price
     * @return Response
     */
    public function acceptImmediatelyOrderFinish(WorkshopOrder $order): Response
    {
        DB::beginTransaction();
        $id = $order->id;

        $order->price = request('price');
        $order->stage = 3;
        $order->save();

        $orderDetails = [
            'id' => $id,
            'price' => request('price'),
        ];

        try {
            ImmediatelyWorkshopOrderFinishJob::dispatch($orderDetails)->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
        return $this->customResponse(true, "order accepting maintenance success", $order);
    }

    public function getPreorders(): Response
    {
        $id = User::find(auth()->user()->id)->workshop->id;
        return $this->customResponse(true,
            'orders list',
            Preorder::orderBy('created_at', 'asc')
                ->where('workshop_id', $id)
                ->paginate(8));
    }

    public function showPreorder(Preorder $order): Response
    {
        $order->user;
        $order->workshop;
        return $this->customResponse(true,
            'order details',
            $order);
    }

    /**
     * @param Preorder $order
     * @return Response
     * @param-out status (0 for reject, 1 for accept)
     */
    public function acceptPreorder(Preorder $order): Response
    {
        DB::beginTransaction();
        $id = $order->id;
        $acceptStatus = request('accept');

        if (request('accept') == 0) {
            return \response(1);
            $order->delete();
            $msg = "order rejecting success";
        } else {
            $path = '/QrCodes/';
            if (!\File::exists(public_path($path))) {
                \File::makeDirectory(public_path($path));
            }
            $time = Carbon::now();
            $time = $time->toDateString() . '_' . $time->hour . '_' . $time->minute . '_' . $time->second;
            $path = 'QrCodes/' . $time . '_qrcode_pre.png';
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd()
            );
            $writer = new Writer($renderer);
            $writer->writeFile('Id: ' . $order->id, $path);
            $order->qr_code = $path;
            $order->stage = $acceptStatus;
            $order->save();
            $msg = "order accepting success";
        }

        $orderDetails = [
            'id' => $id,
            'acceptStatus' => $acceptStatus,
        ];
//        try {
//            ImmediatelyWorkshopOrderJob::dispatch($orderDetails)->onQueue('admin');
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return $this->customResponse(false, 'Bad Internet', null, 504);
//        }
        DB::commit();
        return $this->customResponse(true, $msg, $order);
    }

    /**
     * @param Preorder $order
     * @return Response
     * @param-out price
     */
    public function acceptPreorderFinish(Preorder $order): Response
    {
        DB::beginTransaction();
        $id = $order->id;

        $order->price = request('price');
        $order->stage = 3;
        $order->save();

        $orderDetails = [
            'id' => $id,
            'price' => request('price'),
        ];

//        try {
//            ImmediatelyWorkshopOrderFinishJob::dispatch($orderDetails)->onQueue('admin');
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return $this->customResponse(false, 'Bad Internet', null, 504);
//        }
        DB::commit();
        return $this->customResponse(true, "order accepting maintenance success", $order);
    }
}
