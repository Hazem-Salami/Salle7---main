<?php

namespace App\Http\Controllers\order\towing;

use App\Http\Controllers\Controller;
use App\Models\TowingOrder;
use App\Models\Workshop;
use App\Models\WorkshopOrder;
use App\Services\Towing\Orders\TowingOrderService;
use App\Services\Workshop\Orders\WorkshopOrderService;
use Illuminate\Http\Response;

class TowingOrderController extends Controller
{
    /**
     * The workshop orders service implementation.
     *
     * @var TowingOrderService
     */
    protected TowingOrderService $orderService;

    // singleton pattern, service container
    public function __construct(TowingOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @return Response
     */
    public function getImmediatelyOrders(): Response
    {
        return $this->orderService->getImmediatelyOrders();
    }

    /**
     * @param TowingOrder $order
     * @return Response
     */
    public function showImmediatelyOrder(TowingOrder $order): Response
    {
        return $this->orderService->showImmediatelyOrder($order);
    }

    /**
     * @param TowingOrder $order
     * @return Response
     */
    public function acceptImmediatelyOrder(TowingOrder $order): Response
    {
        return $this->orderService->acceptImmediatelyOrderStartup($order);
    }

    /**
     * @param TowingOrder $order
     * @return Response
     */
    public function acceptImmediatelyOrderMaintenance(TowingOrder $order): Response
    {
        return $this->orderService->acceptImmediatelyOrderMaintenance($order);
    }

    /**
     * @param TowingOrder $order
     * @return Response
     */
    public function acceptImmediatelyOrderFinish(TowingOrder $order): Response
    {
        return $this->orderService->acceptImmediatelyOrderFinish($order);
    }
}
