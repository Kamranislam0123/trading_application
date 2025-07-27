<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no');
            $table->unsignedInteger('buyer_type');
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->date('date');
            $table->float('sub_total', 20);
            $table->float('service_sub_total', 20);
            $table->float('vat_percentage');
            $table->float('service_vat_percentage');
            $table->float('vat', 20);
            $table->float('service_vat', 20);
            $table->float('discount', 20);
            $table->float('service_discount', 20);
            $table->float('total', 20);
            $table->float('paid', 20);
            $table->float('due', 20);
            $table->float('refund', 20)->default(0);
            $table->date('next_payment')->nullable();
            $table->string('received_by')->nullable();
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
        Schema::dropIfExists('sales_orders');
    }
}
