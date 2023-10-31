<?php

namespace App\Http\Controllers\auth\client;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\client\ClientRegisterRequest;
use App\Http\Requests\auth\LoginRequest;
use App\Services\Client\Auth\ClientAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientAuthController extends Controller
{
    /**
     * The auth service implementation.
     *
     * @var ClientAuthService
     */
    protected ClientAuthService $clientAuthService;

    // singleton pattern, service container
    public function __construct(ClientAuthService $clientAuthService)
    {
        $this->clientAuthService = $clientAuthService;
    }

    public function location(Request $request): Response
    {
        return $this->clientAuthService->location($request);
    }

    public function register(ClientRegisterRequest $request): Response
    {
        return $this->clientAuthService->register($request);
    }

    public function login(LoginRequest $request): Response
    {
        return $this->clientAuthService->login($request);
    }

    public function logout(Request $request): Response
    {
        return $this->clientAuthService->logout($request);
    }
}
