<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Services\Wallet\ChargesService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChargesController extends Controller
{
    /**
     * The workshop orders service implementation.
     *
     * @var ChargesService
     */
    protected ChargesService $chargesService;

    // singleton pattern, service container
    public function __construct(ChargesService $chargesService)
    {
        $this->chargesService = $chargesService;
    }

    /**
     * @return Response
     */
    public function createWallet(): Response
    {
        return $this->chargesService->createWallet();
    }

    public function getAmount(): Response
    {
        return $this->chargesService->getAmount();
    }

    public function getStatus(): Response
    {
        return $this->chargesService->getStatus();
    }

}
