<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_operations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_management_id');
            $table->string('client_name');
            $table->string('company_name');
            $table->string('mobile');
            $table->date('date');
            $table->string('remark');
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
        Schema::dropIfExists('client_operations');
    }
}
