<?php

namespace App\Http\Requests\verify;

use App\Http\Requests\BaseRequest;

class VerificationRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|numeric|digits:6',
            'correctCode' => 'required|numeric|digits:6',
        ];
    }
}
