<?php

namespace App\Http\Requests\auth\workshop;

use App\Http\Requests\BaseRequest;

class WorkshopRegisterRequest extends BaseRequest
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
            'workshop_name' => 'required|string|max:35',
            'firstname' => 'required|string|max:25',
            'lastname' => 'required|string|max:25',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users,phone_number',
            'password' => 'required|min:8|max:25',
            'type' => 'required|numeric',
            'description' => 'required|string',
            'address' => 'required|string',
            'fcm_token' => 'required|string|min:8'
        ];
    }
}
