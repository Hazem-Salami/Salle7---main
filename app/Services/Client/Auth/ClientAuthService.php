<?php

namespace App\Services\Client\Auth;

use App\Http\Requests\auth\client\ClientRegisterRequest;
use App\Http\Requests\auth\LoginRequest;
use App\Jobs\auth\client\ClientRegisterJob;
use App\Models\Constant;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClientAuthService extends BaseService
{

    public function location($request)
    {
        $distance = 0;
        return $this->customResponse(true, 'Register Success', $distance);
    }

    /**
     * @param ClientRegisterRequest
     * @return Response
     */
    public function register($request): Response
    {
        DB::beginTransaction();
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'fcm_token' => $request->fcm_token,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('Register Map Token')->accessToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        try {
            ClientRegisterJob::dispatch($user->toArray())->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
//        ClientRegisterJob::dispatch($user->toArray())->onQueue('admin');
        return $this->customResponse(true, 'Register Success', $response);
    }

    /**
     * @param LoginRequest
     * @return Response
     */
    public function login($request): Response
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
        //        config(['auth.guards.user-api.provider' => 'user']);
        if (auth()->attempt($data)) {
            $user = User::find(auth()->user()->id);
//            $this->notify($request->email, $request->email, $request->email, auth()->user()->id);
            $token = $user->createToken('Login Map Token')->accessToken;
            $user->fcm_token = $request->fcm_token;
            $user->save();
            $response = [
                'user' => $user,
                'accessToken' => $token
            ];
            return $this->customResponse(true, 'Login success', $response);
        } else
            return $this->customResponse(false, 'Password is wrong', null, 400);
    }

    /**
     * @param Request
     * @return Response
     */
    public function logout($request): Response
    {
        $request->user()->token()->revoke();
        return $this->customResponse(true, 'Logout success');
    }
}
