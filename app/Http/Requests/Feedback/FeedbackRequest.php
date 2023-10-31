<?php

namespace App\Http\Requests\Feedback;

use App\Http\Requests\BaseRequest;

class FeedbackRequest extends BaseRequest
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
            'evaluator_email' => 'required|email',
            'feedback' => 'required|numeric|min:0|max:100',
        ];
    }
}
