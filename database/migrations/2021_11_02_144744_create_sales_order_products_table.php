<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('sales_order_id');
            $table->unsignedInteger('purchase_inventory_id')->nullable();
            $table->unsignedInteger('product_item_id')->nullable();
            $table->unsignedInteger('product_category_id')->nullable();
            $table->unsignedInteger('product_color_id')->nullable();
            $table->unsignedInteger('product_size_id')->nullable();
            $table->unsignedInteger('warehouse_id')->nullable();
            $table->string('serial')->nullable();
            $table->double('quantity', 20, 2);
            $table->double('unit_price', 20, 2);
            $table->double('total', 20, 2);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('sales_order_products');
    }
}
