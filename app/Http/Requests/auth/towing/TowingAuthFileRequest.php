<?php

namespace App\Http\Requests\auth\towing;

use App\Http\Requests\BaseRequest;
class TowingAuthFileRequest extends BaseRequest
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
            'mechanics_photo' => 'required|array',
            'mechanics_photo.*' => 'max:20000|mimes:bmp,jpg,png,jpeg,svg',
            'certificate_photo' => 'required|array',
            'certificate_photo.*' => 'max:20000|mimes:bmp,jpg,png,jpeg,svg',
            'number' => 'required|string|digits:6',
            'type' => 'required|string|max:25',
            'price' => 'required|numeric',
        ];
    }
}
