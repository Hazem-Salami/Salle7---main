<?php

namespace App\Jobs\auth\storehouse;

use App\Models\Storehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AcceptRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $storehouse = Storehouse::where('email',$this->data['user_email']) -> first();

            $storehouse->latitude = $this->data['latitude'];
            $storehouse->longitude = $this->data['longitude'];
            $storehouse->authenticated = 1;

            $storehouse->update();

        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
