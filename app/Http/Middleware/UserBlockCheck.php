<?php

namespace App\Http\Middleware;

use Closure;
use http\Client\Curl\User;
use Illuminate\Http\Request;

class UserBlockCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if ($user == null || $user->blocked == 0)
            return $next($request);
        else
            return response([
                'status' => false,
                'message' => "تم حظرك مؤقتاً، يرجى التواصل مع فريق الدعم",
                'data' => null
            ], 401);
    }
}
