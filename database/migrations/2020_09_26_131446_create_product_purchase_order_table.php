<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPurchaseOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_purchase_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('purchase_order_id');
            $table->unsignedInteger('product_id');
            $table->string('name');
            $table->string('warranty');
            $table->float('quantity');
            $table->float('unit_price', 20);
            $table->float('including_price', 20);
            $table->float('selling_price', 20);
            $table->float('total', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_purchase_order');
    }
}
