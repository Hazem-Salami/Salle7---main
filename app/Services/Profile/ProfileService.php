<?php

namespace App\Services\Profile;

use App\Models\Category;
use App\Models\User;
use App\Models\Workshop;
use App\Services\BaseService;
use Illuminate\Http\Response;

class ProfileService extends BaseService
{
    /**
     * @return Response
     */
    public function push(): Response
    {
//        $this->notify1("Test notify 1", "test body");;
        return $this->customResponse(true, $this->notify("Test notify", "test body", "test data", 3));
    }

    /**
     * @return Response
     */
    public function getProfileClient(): Response
    {
        $user = auth()->user();
        $user->userfiles;
        return $this->customResponse(true, 'تفاصيل حسابي', $user);
    }

    /**
     * @param User $user
     * @return Response
     */
    public function showProfileClient(User $user): Response
    {
        return $this->customResponse(true, 'تفاصيل حساب مستخدم', $user);
    }

    /**
     * @return Response
     */
    public function getProfileWorkshop(): Response
    {
        $user = auth()->user();
        $user->workshop;
        $user->userfiles;
        return $this->customResponse(true, 'تفاصيل حسابي', $user);
    }

    /**
     * @param Workshop $workshop
     * @return Response
     */
    public function showProfileWorkshop(Workshop $workshop): Response
    {
        $workshop->user;
        $workshop->user->userfiles;
        return $this->customResponse(true, 'تفاصيل حساب ورشة', $workshop);
    }

    /**
     * @return Response
     */
    public function getProfileTowing(): Response
    {
        $user = auth()->user();
        $user->towing;
        return $this->customResponse(true, 'تفاصيل حسابي', $user);
    }

    /**
     * @param User $user
     * @return Response
     */
    public function showProfileTowing(User $user): Response
    {
        $user->towing;
        return $this->customResponse(true, 'تفاصيل حساب سيارة سحب', $user);
    }

    public function updateUserProfile(UpdateProfileRequest $request): Response
    {
        return $this->customResponse(true, 'تم تعديل بياناتك حسابك بنجاح', $user);
    }
}
