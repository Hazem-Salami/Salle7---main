<?php

namespace App\Http\Controllers\order\client;

use App\Http\Controllers\Controller;
use App\Http\Requests\orders\WorkshopPreorderRequest;
use App\Models\Preorder;
use App\Models\Towing;
use App\Models\TowingOrder;
use App\Models\Workshop;
use App\Models\WorkshopOrder;
use App\Services\Client\Orders\ClientOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * The auth service implementation.
     *
     * @var ClientOrderService
     */
    protected ClientOrderService $clientOrderService;

    // singleton pattern, service container
    public function __construct(ClientOrderService $clientOrderService)
    {
        $this->clientOrderService = $clientOrderService;
    }

    /**
     * @return Response
     */
    public function clientLatestOrders(): Response
    {
        return $this->clientOrderService->clientLatestOrders();
    }

    /**
     * @return Response
     */
    public function clientTowingOrders(): Response
    {
        return $this->clientOrderService->clientTowingOrders();
    }

    /**
     * @return Response
     */
    public function clientPreorders(): Response
    {
        return $this->clientOrderService->clientPreorders();
    }

    /**
     * @param Workshop $workshop
     * @param Request $request
     * @return Response
     */
    public function workshopOrder(Workshop $workshop, Request $request): Response
    {
        return $this->clientOrderService->workshopOrder($workshop, $request);
    }

    /**
     * @param WorkshopOrder $order
     * @return Response
     */
    public function getWorkshopOrder(WorkshopOrder $order): Response
    {
        return $this->clientOrderService->getWorkshopOrder($order);
    }

    /**
     * @param WorkshopOrder $order
     * @return Response
     */
    public function payWorkshopOrder(WorkshopOrder $order): Response
    {
        return $this->clientOrderService->payWorkshopOrder($order);
    }

    /**
     * @param Towing $towing
     * @param Request $request
     * @return Response
     */
    public function towingOrder(Towing $towing, Request $request): Response
    {
        return $this->clientOrderService->towingOrder($towing, $request);
    }

    /**
     * @param TowingOrder $order
     * @return Response
     */
    public function getTowingOrder(TowingOrder $order): Response
    {
        return $this->clientOrderService->getTowingOrder($order);
    }

    /**
     * @param TowingOrder $order
     * @return Response
     */
    public function payTowingOrder(TowingOrder $order): Response
    {
        return $this->clientOrderService->payTowingOrder($order);
    }

    /**
     * @param Workshop $workshop
     * @param WorkshopPreorderRequest $request
     * @return Response
     */
    public function preorder(Workshop $workshop, WorkshopPreorderRequest $request): Response
    {
        return $this->clientOrderService->preorder($workshop, $request);
    }

    /**
     * @param Preorder $order
     * @return Response
     */
    public function getPreorder(Preorder $order): Response
    {
        return $this->clientOrderService->getPreorder($order);
    }

    /**
     * @param Preorder $order
     * @return Response
     */
    public function payPreorder(Preorder $order): Response
    {
        return $this->clientOrderService->payPreorder($order);
    }

}
