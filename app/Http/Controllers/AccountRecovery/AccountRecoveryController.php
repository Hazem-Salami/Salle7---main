<?php

namespace App\Http\Controllers\AccountRecovery;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRecovery\ResetPasswordRequest;
use App\Http\Requests\AccountRecovery\SendResetPasswordCodeRequest;
use App\Services\AccountRecovery\AccountRecoveryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountRecoveryController extends Controller
{
    /**
     * The auth service implementation.
     *
     * @var AccountRecoveryService
     */
    protected AccountRecoveryService $accountRecoveryService;

    // singleton pattern, service container
    public function __construct(AccountRecoveryService $accountRecoveryService)
    {
        $this->accountRecoveryService = $accountRecoveryService;
    }

    public function resetPassword(ResetPasswordRequest $request): Response
    {
        return $this->accountRecoveryService->resetPassword($request);
    }

    public function sendResetPasswordCode(SendResetPasswordCodeRequest $request): Response
    {
        return $this->accountRecoveryService->SendResetPasswordCode($request);
    }
}
