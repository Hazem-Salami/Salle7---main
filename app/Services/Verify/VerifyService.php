<?php

namespace App\Services\Verify;

use App\Models\User;
use App\Services\BaseService;
use App\Mail\VerificationMail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\verify\VerificationRequest;
use Carbon\Carbon;

class VerifyService extends BaseService
{
    /**
     *
     * @return Response
     */
    public function SendVerificationCode(): Response
    {
        $user = User::find(auth()->user()->id);

        $code = rand(100000, 999999);

        $contact_data = [
            'fullname' => $user['firstname'] . " " . $user['lastname'],
            'email' => $user['email'],
            'subject' => "Verification Message",
            'message' => $code,
        ];

        try {
            Mail::to($user['email'])->send(new VerificationMail($contact_data));

            $response = [
                'code' => $code,
            ];

            return $this->customResponse(true, 'Send Verification Code Success', $response);
        } catch (\Exception $e) {
            return $this->customResponse(false, 'Send Verification Code Failed', $e->getMessage(), 400);
        }
    }

    /**
     * @param VerificationRequest
     * @return Response
     */
    public function verification($request): Response
    {
        if ($request->correctCode != $request->code) {
            return $this->customResponse(false, 'code is wrong', null, 400);
        }

        $user = User::find(auth()->user()->id);
        $user->email_verified_at = Carbon::now();
        $user->save();

        return $this->customResponse(true, 'code is correct');
    }

    /**
     *
     * @return Response
     */
    public function isVerified(): Response
    {
        return $this->customResponse(true, 'The account is not verified', ['is verified' => false]);
    }

}
