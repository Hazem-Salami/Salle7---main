<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    /*
     * Unify the response
     * with 200 status code
     * first validation message
     * and extend all requests from it
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "status"    =>  FALSE,
            "message"    =>  $validator->errors()->first(),
            "data"    =>  null
        ], 400));
    }
}
