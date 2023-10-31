<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkshopTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workshop_times', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->time('time_from');
            $table->time('time_to');
            $table->bigInteger('workshop_id')->unsigned();

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
        Schema::dropIfExists('workshop_times');
    }
}
