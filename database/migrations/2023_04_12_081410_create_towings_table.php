<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('towings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('number')->nullable();
            $table->string('type')->nullable();
            $table->double('price')->nullable();
            $table->tinyInteger('is_active')->default(0);
            $table->tinyInteger('authenticated')->default(0);
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('towings');
    }
};
