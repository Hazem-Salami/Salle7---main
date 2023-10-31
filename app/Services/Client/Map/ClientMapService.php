<?php

namespace App\Services\Client\Map;

use App\Models\Towing;
use App\Models\Workshop;
use App\Services\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClientMapService extends BaseService
{
    public function home($request): Response
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $workshops = DB::table('workshops')
            ->select('id', 'name', 'latitude', 'longitude', 'is_active', DB::raw(sprintf(
                '(6371 * acos(cos(radians(%1$.7f)) * cos(radians(latitude)) * cos(radians(longitude) - radians(%2$.7f)) + sin(radians(%1$.7f)) * sin(radians(latitude)))) AS distance',
                $latitude,
                $longitude
            )))
            ->where('authenticated', '=', 1)
            ->having('distance', '<=', 150)
            ->orderBy('distance', 'asc')
            ->get();

        $towingCars = DB::table('towings')
            ->select('id', 'latitude', 'longitude', 'is_active', DB::raw(sprintf(
                '(6371 * acos(cos(radians(%1$.7f)) * cos(radians(latitude)) * cos(radians(longitude) - radians(%2$.7f)) + sin(radians(%1$.7f)) * sin(radians(latitude)))) AS distance',
                $latitude,
                $longitude
            )))
            ->where('is_active', 1)
            ->where('authenticated', '=', 1)
            ->having('distance', '<=', 150)
            ->orderBy('distance', 'asc')
            ->get();

        if (count($workshops) <= 0) {
            $workshops = Workshop::all();
            $towingCars = Towing::all();
        }

        $response = [
            'workshops' => $workshops,
            'towing' => $towingCars
        ];
        return $this->customResponse(true, 'workshops and towing cars', $response);
    }
}
