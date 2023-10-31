<?php

namespace App\Http\Controllers\order\workshop;

use App\Http\Controllers\Controller;
use App\Models\Preorder;
use App\Models\Workshop;
use App\Models\WorkshopOrder;
use App\Services\Workshop\Orders\WorkshopOrderService;
use Illuminate\Http\Response;

class WorkshopOrderController extends Controller
{
    /**
     * The workshop orders service implementation.
     *
     * @var WorkshopOrderService
     */
    protected WorkshopOrderService $orderService;

    // singleton pattern, service container
    public function __construct(WorkshopOrderService $orderService)
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
     * @param WorkshopOrder $order
     * @return Response
     */
    public function showImmediatelyOrder(WorkshopOrder $order): Response
    {
        return $this->orderService->showImmediatelyOrder($order);
    }

    /**
     * @param WorkshopOrder $order
     * @return Response
     */
    public function acceptImmediatelyOrder(WorkshopOrder $order): Response
    {
        return $this->orderService->acceptImmediatelyOrderStartup($order);
    }

    /**
     * @param WorkshopOrder $order
     * @return Response
     */
    public function acceptImmediatelyOrderMaintenance(WorkshopOrder $order): Response
    {
        return $this->orderService->acceptImmediatelyOrderMaintenance($order);
    }

    /**
     * @param WorkshopOrder $order
     * @return Response
     */
    public function acceptImmediatelyOrderFinish(WorkshopOrder $order): Response
    {
        return $this->orderService->acceptImmediatelyOrderFinish($order);
    }

    /**
     * @return Response
     */
    public function getPreorders(): Response
    {
        return $this->orderService->getPreorders();
    }

    /**
     * @param Preorder $order
     * @return Response
     */
    public function showPreorder(Preorder $order): Response
    {
        return $this->orderService->showPreorder($order);
    }

    /**m
     *
     * @param Preorder $order
     * @return Response
     */
    public function acceptPreorder(Preorder $order): Response
    {
        return $this->orderService->acceptPreorder($order);
    }

    /**
     * @param Preorder $order
     * @return Response
     */
    public function acceptPreorderFinish(Preorder $order): Response
    {
        return $this->orderService->acceptPreorderFinish($order);
    }
}
