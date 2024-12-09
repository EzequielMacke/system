<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInputUsedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('input_useds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('order_services');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('status');
            $table->date('date_created');
            $table->unsignedInteger('constructionsite_id');
            $table->foreign('constructionsite_id')->references('id')->on('construction_site');
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
        Schema::dropIfExists('input_useds');
    }
}
