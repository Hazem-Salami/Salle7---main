<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->bigInteger('storehouse_id')->unsigned();
            $table->foreign('storehouse_id')
                ->references('id')
                ->on('storehouses')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            /**
             * @ref payment_method
             * 0  cash
             * 1  coins
             */
            $table->boolean('payment_method');

            /**
             * @ref stage
             * null waiting
             * 0 accepted
             * 1 rejected
             */
            $table->boolean('stage')->nullable();

            $table->bigInteger('quantity');

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
        Schema::dropIfExists('purchase_orders');
    }
}
