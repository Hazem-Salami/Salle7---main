<?php

namespace App\Services\Towing\Orders;

use App\Jobs\orders\towing\ImmediatelyTowingOrderFinishJob;
use App\Jobs\orders\towing\ImmediatelyTowingOrderJob;
use App\Jobs\orders\towing\ImmediatelyTowingOrderMaintenanceJob;
use App\Jobs\orders\workshop\ImmediatelyWorkshopOrderFinishJob;
use App\Jobs\orders\workshop\ImmediatelyWorkshopOrderJob;
use App\Jobs\orders\workshop\ImmediatelyWorkshopOrderMaintenanceJob;
use App\Models\TowingOrder;
use App\Models\Workshop;
use App\Models\WorkshopOrder;
use App\Services\BaseService;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TowingOrderService extends BaseService
{
    public function getImmediatelyOrders(): Response
    {
        return $this->customResponse(true,
            'orders list',
            TowingOrder::orderBy('created_at', 'asc')->paginate(8));
    }

    public function showImmediatelyOrder(TowingOrder $order): Response
    {
        $order->user;
        $order->towing;
        $order->towing->user;
        return $this->customResponse(true,
            'order details',
            $order);
    }

    /**
     * @param TowingOrder $order
     * @param-out status (0 for reject, 1 for accept)
     * @return Response
     */
    public function acceptImmediatelyOrderStartup(TowingOrder $order): Response
    {
        if (!\File::exists(public_path('/QrCodes/'))) {
            \File::makeDirectory(public_path('/QrCodes/'));
        }
        $path = '/QrCodes/';
        if (!\File::exists(public_path($path))) {
            \File::makeDirectory(public_path($path));
        }
        $time = Carbon::now();
        $time = $time->toDateString() . '_' . $time->hour . '_' . $time->minute . '_' . $time->second;
        $path = 'QrCodes/' . $time . '_qrcode.png';
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $writer->writeFile('Id: ' . $order->id, $path);
        DB::beginTransaction();
        $id = $order->id;
        $acceptStatus = request('accept');

        if (request('accept') == 0) {
            $order->delete();
            $msg = "order rejecting success";
        } else {
            TowingOrder::where('user_id', $order->user_id)
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
            ImmediatelyTowingOrderJob::dispatch($orderDetails)->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
        return $this->customResponse(true, $msg, $order);
    }

    /**
     * @param TowingOrder $order
     * @param-out status (0 in workshop, 1 on road)
     * @return Response
     */
    public function acceptImmediatelyOrderMaintenance(TowingOrder $order): Response
    {
        $time = Carbon::now();
        $time = $time->toDateString() . '_' . $time->hour . '_' . $time->minute . '_' . $time->second;
        $path = '/QrCodes/' . $time . '_qrcode.png';
        QrCode::size(250)
            ->format('png')
            ->color(74, 139, 223)
            ->generate("id:" . $order->id, public_path($path));
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
            ImmediatelyTowingOrderMaintenanceJob::dispatch($orderDetails)->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
        return $this->customResponse(true, "order accepting success", $order);
    }

    /**
     * @param TowingOrder $order
     * @param-out price
     * @return Response
     */
    public function acceptImmediatelyOrderFinish(TowingOrder $order): Response
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
            ImmediatelyTowingOrderFinishJob::dispatch($orderDetails)->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
        return $this->customResponse(true, "order accepting success", $order);
    }
}
