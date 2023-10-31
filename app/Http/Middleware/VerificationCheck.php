<?php

namespace App\Http\Middleware;

use App\Http\Traits\ResponseTrait;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class VerificationCheck
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = User::find(auth()->user()->id);

        if($user->email_verified_at == null) {

            return $next($request);

        }else{

            return $this->customResponse(false, 'The account is verified', ['is verified' => true], 400);
        }
    }
}
