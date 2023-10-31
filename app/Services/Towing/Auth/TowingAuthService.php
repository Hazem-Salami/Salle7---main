<?php

namespace App\Services\Towing\Auth;

use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\towing\TowingAuthFileRequest;
use App\Http\Requests\auth\workshop\WorkshopRegisterRequest;
use App\Http\Traits\Base64Trait;
use App\Jobs\auth\towing\TowingActiveJob;
use App\Jobs\auth\towing\TowingRegisterJob;
use App\Jobs\auth\towing\SendAuthFilesJob;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TowingAuthService extends BaseService
{
    use Base64Trait;

    /**
     * @param WorkshopRegisterRequest
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
            'user_type' => 2,
            'password' => bcrypt($request->password)
        ]);

        $user->towing()->create();

        $token = $user->createToken('Register Towing Token')->accessToken;
        $user->towing;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        try {
            TowingRegisterJob::dispatch($user->toArray())->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
        return $this->customResponse(true, 'Towing Register Success', $response);
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
            $token = $user->createToken('Login Towing Token')->accessToken;
            $user->towing;

            $response = [
                'user' => $user,
                'accessToken' => $token
            ];
            return $this->customResponse(true, 'Towing Login success', $response);
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
        return $this->customResponse(true, 'Towing Logout success');
    }

    /**
     * @param TowingAuthFileRequest
     * @return Response
     */
    public function sendAuthFiles($request): Response
    {
        DB::beginTransaction();

        $user = User::find(auth()->user()->id);

        $towing = $user->towing;

        if ($towing->authenticated == 0 || $towing->authenticated % 2 != 0 && $towing->authenticated >= 3) {

            $mechanics_photo = array();

            if ($request->has('mechanics_photo')) {

                $files = $request->file('mechanics_photo');

                if ($files != null) {

                    foreach ($files as $file) {
                        $mechanics_photo[] = [$this->base64Encode($file), $file->getClientOriginalExtension()];
                    }
                }
            }

            $certificate_photo = array();

            if ($request->has('certificate_photo')) {

                $files = $request->file('certificate_photo');

                if ($files != null) {

                    foreach ($files as $file) {

                        $certificate_photo[] = [$this->base64Encode($file), $file->getClientOriginalExtension()];
                    }
                }
            }

            if ($towing->authenticated == 0)
                $towing->authenticated = 2;
            else
                $towing->authenticated++;
            $towing->update();

            $response = [
                'mechanics_photo' => $mechanics_photo,
                'certificate_photo' => $certificate_photo,
                'number' => $request->number,
                'type' => $request->type,
                'price' => $request->price,
                'user_email' => $user->email,
            ];

            try {

                SendAuthFilesJob::dispatch($response)->onQueue('admin');

            } catch (\Exception $e) {
                DB::rollBack();
                return $this->customResponse(false, 'Bad Internet', null, 504);
            }
            DB::commit();

            return $this->customResponse(true, "send auth files success", null);

        } else
            return $this->customResponse(false, 'already sent', null, 400);
    }

    public function getActiveStatus(): Response
    {
        $car = auth()->user()->towing;
        return $this->customResponse(true, 'active status', $car->is_active);
    }

    public function active(): Response
    {
        DB::beginTransaction();
        $latitude = \request('latitude');
        $longitude = \request('longitude');
        $user = auth()->user();
        $car = $user->towing;

        if ($latitude == null && $longitude == null) {
            $latitude = $car->latitude;
            $longitude = $car->longitude;
        }

        $car->update([
            'is_active' => \request('isActive'),
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
        try {

            $queue = [
                'email' => $user->email,
                'is_active' => \request('isActive'),
                'latitude' => $latitude,
                'longitude' => $longitude
            ];
            TowingActiveJob::dispatch($queue)->onQueue('admin');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();
        return $this->customResponse(true, 'active toggling success', $car);
    }
}
