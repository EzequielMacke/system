<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_complaints', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->date('date');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('order_services');
            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('constructionsite_id');
            $table->foreign('contructionsite_id')->references('id')->on('construction_site');
            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches');
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
        Schema::dropIfExists('customer_complaints');
    }
}
