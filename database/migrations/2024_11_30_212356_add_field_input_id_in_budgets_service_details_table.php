<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldInputIdInBudgetsServiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('budgets_service_details', function (Blueprint $table) {
            $table->integer('level')->after('price');
            $table->unsignedInteger('input_id')->after('total_price');
            $table->foreign('input_id')->references('id')->on('inputs');
            $table->integer('quantity_per_meter')->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('budgets_service_details', function (Blueprint $table) {
            //
        });
    }
}
