<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgets_service', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->unsignedInteger('wish_service_id');
            $table->foreign('wish_service_id')->references('id')->on('wish_services');
            $table->unsignedInteger('constructionsite_id');
            $table->foreign('constructionsite_id')->references('id')->on('construction_site');
            $table->date('date_budgets');
            $table->integer('tax');
            $table->integer('currency');
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
        Schema::dropIfExists('budgets_service');
    }
}
