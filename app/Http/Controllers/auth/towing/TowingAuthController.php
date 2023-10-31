<?php

namespace App\Http\Controllers\auth\towing;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\towing\TowingRegisterRequest;
use App\Http\Requests\auth\towing\TowingAuthFileRequest;
use App\Services\Towing\Auth\TowingAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TowingAuthController extends Controller
{
    /**
     * The auth service implementation.
     *
     * @var TowingAuthService
     */
    protected TowingAuthService $towingAuthService;

    // singleton pattern, service container
    public function __construct(TowingAuthService $towingAuthService)
    {
        $this->towingAuthService = $towingAuthService;
    }

    public function register(TowingRegisterRequest $request): Response
    {
        return $this->towingAuthService->register($request);
    }

    public function login(LoginRequest $request): Response
    {
        return $this->towingAuthService->login($request);
    }

    public function logout(Request $request): Response
    {
        return $this->towingAuthService->logout($request);
    }

    public function sendAuthFiles(TowingAuthFileRequest $request): Response
    {
        return $this->towingAuthService->sendAuthFiles($request);
    }

    public function getActiveStatus(): Response
    {
        return $this->towingAuthService->getActiveStatus();
    }

    public function active(): Response
    {
        return $this->towingAuthService->active();
    }
}
