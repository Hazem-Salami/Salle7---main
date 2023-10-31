<?php

namespace App\Services\AccountRecovery;

use App\Http\Requests\AccountRecovery\ResetPasswordRequest;
use App\Http\Requests\AccountRecovery\SendResetPasswordCodeRequest;
use App\Mail\VerificationMail;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class AccountRecoveryService extends BaseService
{
    /**
     * @param ResetPasswordRequest
     * @return Response
     */
    public function resetPassword(ResetPasswordRequest $request): Response
    {
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->newPassword);
        $user->update();

        return $this->customResponse(true, 'تم تغير كلمة المرور بنجاح', $user);
    }

    /**
     * @param SendResetPasswordCodeRequest
     * @return Response
     */
    public function sendResetPasswordCode(SendResetPasswordCodeRequest $request): Response
    {
        $user = User::where('email', $request->email)->first();

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

            return $this->customResponse(true, 'تم ارسال كود التحقق بنجاح', $response);
        } catch (\Exception $e) {
            return $this->customResponse(false, 'حدثت مشكلة في ارسال كود التحقق الرجاء المحاولة لاحقاً', $e->getMessage(), 400);
        }
    }
}
