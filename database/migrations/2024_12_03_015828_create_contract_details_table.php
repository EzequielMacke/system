<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('contract_id');
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->unsignedInteger('obligation_id');
            $table->foreign('obligation_id')->references('id')->on('obligations');
            $table->unsignedInteger('clause_id');
            $table->foreign('clause_id')->references('id')->on('clauses');
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
        Schema::dropIfExists('contract_details');
    }
}
