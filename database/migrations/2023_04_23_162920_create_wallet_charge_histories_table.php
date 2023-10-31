<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletChargeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_charge_histories', function (Blueprint $table) {
            $table->id();
            $table->double('pre_mount')->unsigned()->nullable();
            $table->double('new_amount')->unsigned()->nullable();
            $table->double('charge')->unsigned()->nullable();
            $table->bigInteger('wallet_id')->unsigned();
            /**
             * 0 charge
             * 1 withdraw
             */
            $table->tinyInteger('type')->nullable()->default(0);

            $table->foreign('wallet_id')
                ->references('id')
                ->on('user_wallets')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_charge_histories');
    }
}
