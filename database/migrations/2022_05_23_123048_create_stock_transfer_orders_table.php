<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransferOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no')->nullable();
            $table->unsignedBigInteger('sourch_warehouse_id');
            $table->unsignedBigInteger('target_warehouse_id');
            $table->date('date');
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('stock_transfer_orders');
    }
}
