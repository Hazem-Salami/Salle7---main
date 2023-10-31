<?php

namespace App\Http\Requests\AccountRecovery;

use App\Http\Requests\BaseRequest;

class ResetPasswordRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'newPassword' => 'required|string|min:8|max:25',
            'confirmPassword' => 'required|string|min:8|max:25|same:newPassword',
            'code' => 'required|numeric|digits:6',
            'correctCode' => 'required|numeric|digits:6|same:code',
            'email' => 'required|email|exists:users,email',
        ];
    }
}
