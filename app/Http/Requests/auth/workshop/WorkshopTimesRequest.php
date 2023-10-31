<?php

namespace App\Http\Requests\auth\workshop;

use App\Http\Requests\BaseRequest;

class WorkshopTimesRequest extends BaseRequest
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
            'time_from' => 'required|string|max:35',
            'time_to' => 'required|string|max:35',
            'days' => 'required|array',
            'days.*' => 'max:35|numeric',
        ];
    }
}
