<?php

namespace App\Services\Workshop\Times;

use App\Http\Requests\auth\workshop\WorkshopTimesRequest;
use App\Models\Workshop;
use App\Models\WorkshopTimes;
use App\Services\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class WorkshopTimesService extends BaseService
{
    /**
     * @param WorkshopTimesRequest $request
     * @param Workshop $workshop
     * @return Response
     */
    public function setTimes(WorkshopTimesRequest $request, Workshop $workshop): Response
    {
        DB::beginTransaction();
        $days = $request->input('days');

        foreach ($days as $day) {
            $record = WorkshopTimes::where('day', $day)
                ->where('workshop_id', $workshop->id)
                ->first();

            if ($record == null)
                $workshop->times()->create([
                    'time_from' => $request->time_from,
                    'time_to' => $request->time_to,
                    'day' => $day
                ]);
            else
                $record->update([
                    'time_from' => $request->time_from,
                    'time_to' => $request->time_to,
                    'day' => $day
                ]);
        }
        $workshop->times;
        DB::commit();
        return $this->customResponse(true, 'Workshop times updated successfully', $workshop);
    }

    /**
     * @param Workshop $workshop
     * @return Response
     */
    public function getTimes(Workshop $workshop): Response
    {
        $workshop->times;
        return $this->customResponse(true, 'Workshop times updated successfully', $workshop);
    }
}
