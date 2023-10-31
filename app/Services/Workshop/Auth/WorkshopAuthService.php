<?php

namespace App\Services\Workshop\Auth;

use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\workshop\WorkshopRegisterRequest;
use App\Http\Requests\auth\workshop\WorkshopAuthFileRequest;
use App\Http\Traits\FilesTrait;
use App\Jobs\auth\workshop\WorkshopRegisterJob;
use App\Jobs\auth\workshop\SendAuthFilesJob;
use App\Models\User;
use App\Models\Workshop;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Traits\Base64Trait;
use Illuminate\Support\Facades\DB;

class WorkshopAuthService extends BaseService
{
    use Base64Trait, FilesTrait;

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
            'user_type' => 1,
            'password' => bcrypt($request->password)
        ]);

        $workshop = $user->workshop()->create([
            'name' => $request->workshop_name,
            'address' => $request->address,
            'type' => $request->type,
            'description' => $request->description,
        ]);

        $token = $user->createToken('Register Workshop Token')->accessToken;
        $user->workshop;

        $response = [
            'user' => $user,
            'token' => $token
        ];


        try {

            WorkshopRegisterJob::dispatch($user->toArray())->onQueue('admin');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();

        return $this->customResponse(true, 'Workshop Register Success', $response);
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
            $token = $user->createToken('Login Workshop Token')->accessToken;
            $user->workshop;

            $response = [
                'user' => $user,
                'accessToken' => $token
            ];
            return $this->customResponse(true, 'Workshop Login success', $response);
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
        return $this->customResponse(true, 'Workshop Logout success');
    }

    /**
     * @param WorkshopAuthFileRequest
     * @return Response
     */
    public function sendAuthFiles(WorkshopAuthFileRequest $request): Response
    {
        DB::beginTransaction();

        $user = User::find(auth()->user()->id);

        $workshop = $user->workshop;

        if ($workshop->authenticated == 0 || $workshop->authenticated % 2 != 0 && $workshop->authenticated >= 3) {

            $workshop_photo = array();

            if ($request->has('workshop_photo')) {

                $files = $request->file('workshop_photo');

                if ($files != null) {
                    $i=0;
                    foreach ($files as $file) {
                        $i++;

                        $workshop_photo[] = [$this->base64Encode($file), $file->getClientOriginalExtension()];

                        $path = $this->storeFile($file,'workshop_photo', $i);

                        $user->userfiles()->create([
                            'path' => $path,
                        ]);
                    }
                }
            }

            $IDphoto = array();

            if ($request->has('IDphoto')) {

                $files = $request->file('IDphoto');

                if ($files != null) {

                    foreach ($files as $file) {

                        $IDphoto[] = [$this->base64Encode($file), $file->getClientOriginalExtension()];
                    }
                }
            }

            if($workshop->authenticated == 0)
                $workshop->authenticated = 2;
            else
                $workshop->authenticated++;

            $workshop->update();

            $response = [
                'workshop_photo' => $workshop_photo,
                'IDphoto' => $IDphoto,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'user_email' => $user->email,
            ];

            try {

                SendAuthFilesJob::dispatch($response)->onQueue('admin');

            } catch (\Exception $e) {
                DB::rollBack();
                return $this->customResponse(false, 'Bad Internet', null, 504);
            }
            DB::commit();

            return $this->customResponse(true, "send auth files success");

        } else
            return $this->customResponse(false, 'already sent', null, 400);
    }

    public function getActiveStatus(): Response
    {
        $workshop = auth()->user()->workshop;
        return $this->customResponse(true, 'active status', $workshop->is_active);
    }

    public function active(): Response
    {
        $workshop = auth()->user()->workshop;
        $workshop->is_active = \request('isActive');
        $workshop->save();
        return $this->customResponse(true, 'active toggling success', $workshop->is_active);
    }
}
