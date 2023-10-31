<?php

namespace App\Http\Controllers\auth\workshop;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\workshop\WorkshopTimesRequest;
use App\Models\Workshop;
use App\Services\Workshop\Times\WorkshopTimesService;
use Illuminate\Http\Response;

class WorkshopTimesController extends Controller
{
    /**
     * The auth service implementation.
     *
     * @var WorkshopTimesService
     */
    protected WorkshopTimesService $workshopTimesService;

    // singleton pattern, service container
    public function __construct(WorkshopTimesService $workshopTimesService)
    {
        $this->workshopTimesService = $workshopTimesService;
    }

    public function setTimes(WorkshopTimesRequest $request, Workshop $workshop): Response
    {
        return $this->workshopTimesService->setTimes($request, $workshop);
    }

    public function getTimes(Workshop $workshop): Response
    {
        return $this->workshopTimesService->getTimes($workshop);
    }
}
