<?php

namespace App\Jobs\wallets;

use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChargeWalletJob implements ShouldQueue
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
            $fields = $this->data;
            $user = User::where("email", $fields["user_email"])->first();
            $wallet = $user->wallet;
            $preAmount = $wallet->amount;
            $wallet->amount += $fields["charge"];
            $wallet->save();

            $wallet->charges()->create([
                'charge' => $fields["charge"],
                'new_amount' => $wallet->amount,
                'pre_mount' => $preAmount
            ]);
        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
