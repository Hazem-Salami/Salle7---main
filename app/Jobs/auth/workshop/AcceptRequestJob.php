<?php

namespace App\Jobs\auth\workshop;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
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

            $user = User::where('email',$this->data['user_email']) -> first();

            $workshop = $user->workshop;

            $workshop->latitude = $this->data['latitude'];
            $workshop->longitude = $this->data['longitude'];
            $workshop->authenticated = 1;

            $workshop->update();

        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
