<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preorders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('workshop_id')->unsigned();
            $table->float('price')->nullable();
            /**
             * @ref payment_method
             * 0  cash
             * 1  coins
             */
            $table->tinyInteger('payment_method')->nullable();
            /**
             * @ref stage
             * 0  waiting
             * 1  startup
             * 2  maintenance
             * 3  done
             */
            $table->tinyInteger('stage')->nullable();
            $table->string('description')->nullable();
            $table->string('address')->nullable();
            $table->float('user_latitude')->nullable();
            $table->float('user_longitude')->nullable();
            $table->string('qr_code')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('workshop_id')
                ->references('id')
                ->on('workshops')
                ->onUpdate('cascade')
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
        Schema::dropIfExists('preorders');
    }
}
