<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldServiceIdInContractDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_details', function (Blueprint $table) {
            $table->unsignedInteger('service_id')->nullable()->after('contract_id');
            $table->foreign('service_id')->references('id')->on('services');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_details', function (Blueprint $table) {
            //
        });
    }
}
