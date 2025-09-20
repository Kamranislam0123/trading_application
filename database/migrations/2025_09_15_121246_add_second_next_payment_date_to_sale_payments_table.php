<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecondNextPaymentDateToSalePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->date('second_next_payment_date')->nullable()->after('next_approximate_payment_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->dropColumn('second_next_payment_date');
        });
    }
}
