<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->date('date_created');
            $table->date('date_signed');
            $table->unsignedInteger('constructionsite_id');
            $table->foreign('constructionsite_id')->references('id')->on('construction_site');
            $table->integer('term');
            $table->unsignedInteger('budget_service_id');
            $table->foreign('budget_service_id')->references('id')->on('budgets_service');
            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('placement');
            $table->string('issue');
            $table->integer('status');
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
        Schema::dropIfExists('contracts');
    }
}
