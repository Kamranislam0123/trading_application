<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_management', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('marketing_id');
            $table->string('client_name');
            $table->string('company_name');
            $table->string('mobile');
            $table->tinyInteger('source');
            $table->string('address');
            $table->tinyInteger('status');
            $table->float('propose_amount');
            $table->date('date');
            $table->string('comments');
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
        Schema::dropIfExists('client_management');
    }
}
