<?php

namespace App\Jobs\auth\storehouse;

use App\Http\Traits\Base64Trait;
use App\Models\Storehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RejectRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Base64Trait;

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

            if($storehouse->authenticated == 0)
                $storehouse->authenticated = 2;

            $storehouse->authenticated++;

            $storehouse->update();

        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
