<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateRangeToEmployeeTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_targets', function (Blueprint $table) {
            $table->date('from_date')->nullable()->after('year');
            $table->date('to_date')->nullable()->after('from_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_targets', function (Blueprint $table) {
            $table->dropColumn(['from_date', 'to_date']);
        });
    }
}
