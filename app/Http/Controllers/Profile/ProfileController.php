<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workshop;
use App\Services\Profile\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    /**
     * The workshop orders service implementation.
     *
     * @var ProfileService
     */
    protected ProfileService $profileService;

    // singleton pattern, service container
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * @return Response
     */
    public function push(): Response
    {
        return $this->profileService->push();
    }

    /**
     * @return Response
     */
    public function getProfileClient(): Response
    {
        return $this->profileService->getProfileClient();
    }

    /**
     * @param User $user
     * @return Response
     */
    public function showProfileClient(User $user): Response
    {
        return $this->profileService->showProfileClient($user);
    }

    /**
     * @return Response
     */
    public function getProfileWorkshop(): Response
    {
        return $this->profileService->getProfileWorkshop();
    }

    /**
     * @param Workshop $workshop
     * @return Response
     */
    public function showProfileWorkshop(Workshop $workshop): Response
    {
        return $this->profileService->showProfileWorkshop($workshop);
    }

    /**
     * @return Response
     */
    public function getProfileTowing(): Response
    {
        return $this->profileService->getProfileTowing();
    }

    /**
     * @param User $user
     * @return Response
     */
    public function showProfileTowing(User $user): Response
    {
        return $this->profileService->showProfileTowing($user);
    }

    /**
     * @param UpdateProfileRequest $request
     * @return Response
     */
    public function updateUserProfile(UpdateProfileRequest $request): Response
    {

    }
}
