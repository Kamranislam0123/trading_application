<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnAmountToSalePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->decimal('return_amount', 20, 2)->nullable()->after('receive_amount');
            $table->date('return_date')->nullable()->after('return_amount');
            $table->text('return_reason')->nullable()->after('return_date');
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
            $table->dropColumn(['return_amount', 'return_date', 'return_reason']);
        });
    }
}
