<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualStockOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_stock_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('warehouse_id');
            $table->date('date');
            $table->float('total', 20);
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
        Schema::dropIfExists('manual_stock_orders');
    }
}
