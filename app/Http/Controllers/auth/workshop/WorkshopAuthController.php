<?php

namespace App\Http\Controllers\auth\workshop;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\workshop\WorkshopRegisterRequest;
use App\Http\Requests\auth\workshop\WorkshopAuthFileRequest;
use App\Services\Workshop\Auth\WorkshopAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WorkshopAuthController extends Controller
{
    /**
     * The auth service implementation.
     *
     * @var WorkshopAuthService
     */
    protected WorkshopAuthService $workshopAuthService;

    // singleton pattern, service container
    public function __construct(WorkshopAuthService $workshopAuthService)
    {
        $this->workshopAuthService = $workshopAuthService;
    }

    public function register(WorkshopRegisterRequest $request): Response
    {
        return $this->workshopAuthService->register($request);
    }

    public function login(LoginRequest $request): Response
    {
        return $this->workshopAuthService->login($request);
    }

    public function logout(Request $request): Response
    {
        return $this->workshopAuthService->logout($request);
    }

    public function sendAuthFiles(WorkshopAuthFileRequest $request): Response
    {
        return $this->workshopAuthService->sendAuthFiles($request);
    }

    public function getActiveStatus(): Response
    {
        return $this->workshopAuthService->getActiveStatus();
    }

    public function active(): Response
    {
        return $this->workshopAuthService->active();
    }
}
