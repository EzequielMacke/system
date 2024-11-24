<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsServiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgets_service_details', function (Blueprint $table) {
            $table->integer('id');
            $table->unsignedInteger('budget_service_id');
            $table->foreign('budget_service_id')->references('id')->on('budgets_service');
            $table->unsignedInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services');
            $table->integer('quantity');
            $table->integer('price');
            $table->integer('total_price');
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
        Schema::dropIfExists('budgets_service_details');
    }
}
