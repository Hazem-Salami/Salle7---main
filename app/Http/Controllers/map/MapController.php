<?php

namespace App\Http\Controllers\map;

use App\Http\Controllers\Controller;
use App\Services\Client\Map\ClientMapService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MapController extends Controller
{
    /**
     * The auth service implementation.
     *
     * @var ClientMapService
     */
    protected ClientMapService $clientMapService;

    // singleton pattern, service container
    public function __construct(ClientMapService $clientMapService)
    {
        $this->clientMapService = $clientMapService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function home(Request $request): Response
    {
        return $this->clientMapService->home($request);
    }
}
