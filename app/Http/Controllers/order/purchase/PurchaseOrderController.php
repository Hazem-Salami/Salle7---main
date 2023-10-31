<?php

namespace App\Http\Controllers\order\purchase;

use App\Http\Controllers\Controller;
use App\Http\Requests\order\purchase\SendPurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Services\Order\Purchase\PurchaseOrderService;
use Illuminate\Http\Response;

class PurchaseOrderController extends Controller
{
    /**
     * The workshop orders service implementation.
     *
     * @var PurchaseOrderService
     */
    protected PurchaseOrderService $purchaseOrderService;

    // singleton pattern, service container
    public function __construct(PurchaseOrderService $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }

    /**
     * @param SendPurchaseOrderRequest $request
     * @return Response
     */
    public function sendPurchaseOrder(SendPurchaseOrderRequest $request): Response
    {
        return $this->purchaseOrderService->sendPurchaseOrder($request);
    }

    /**
     * @return Response
     */
    public function getWaitingPurchaseOrders(): Response
    {
        return $this->purchaseOrderService->getWaitingPurchaseOrders();
    }

    /**
     * @return Response
     */
    public function getAcceptedPurchaseOrders(): Response
    {
        return $this->purchaseOrderService->getAcceptedPurchaseOrders();
    }

    /**
     * @return Response
     */
    public function getRejectedPurchaseOrders(): Response
    {
        return $this->purchaseOrderService->getRejectedPurchaseOrders();
    }

    /**
     * @return Response
     */
    public function donePurchaseOrders(PurchaseOrder $purchaseOrder): Response
    {
        return $this->purchaseOrderService->donePurchaseOrders($purchaseOrder);
    }
}
