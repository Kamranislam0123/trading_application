<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReturnOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_return_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('company_branch_id');
            $table->date('date');
            $table->float('total', 20);
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
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
        Schema::dropIfExists('product_return_orders');
    }
}
