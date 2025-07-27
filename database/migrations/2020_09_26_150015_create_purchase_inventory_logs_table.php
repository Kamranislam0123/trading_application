<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseInventoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_inventory_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('product_id');
            $table->tinyInteger('type')->comment('1=In; 2=Out');
            $table->date('date');
            $table->unsignedInteger('warehouse_id');
            $table->float('quantity', 20);
            $table->float('unit_price', 20)->nullable();
            $table->float('including_price', 20)->nullable();
            $table->float('selling_price', 20)->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->unsignedInteger('sales_order_id')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('purchase_inventory_logs');
    }
}
