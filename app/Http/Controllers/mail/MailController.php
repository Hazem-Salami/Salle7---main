<?php

namespace App\Http\Controllers\mail;

use App\Http\Controllers\Controller;
use App\Services\Verify\VerifyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\verify\VerificationRequest;

class MailController extends Controller
{
    /**
     * The verify service implementation.
     *
     * @var VerifyService
     */
    protected VerifyService $VerifyService;

    // singleton pattern, service container
    public function __construct(VerifyService $VerifyService)
    {
        $this->VerifyService = $VerifyService;
    }

    public function verification(VerificationRequest $request): Response
    {
        return $this->VerifyService->verification($request);
    }

    public function SendVerificationCode(): Response
    {
        return $this->VerifyService->SendVerificationCode();
    }


    public function isVerified(): Response
    {
        return $this->VerifyService->isVerified();
    }
}
