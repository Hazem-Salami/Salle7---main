<?php

namespace App\Http\Traits;

use \Illuminate\Http\Response;
use \Illuminate\Contracts\Foundation\Application;
use \Illuminate\Contracts\Routing\ResponseFactory;

trait ResponseTrait
{
    // helper function to global responses
    public function customResponse($status, $msg, $data = null, $code = 200): Response|Application|ResponseFactory
    {
        $response = [
            'status' => $status,
            'message' => $msg,
            'data' => $data,
        ];
        return response($response, $code);
    }
}
