<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWishServiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wish_service_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('services_id');
            $table->foreign('services_id')->references('id')->on('services');
            $table->unsignedInteger('wish_services_id');
            $table->foreign('wish_services_id')->references('id')->on('wish_services');
            $table->integer('quantity');
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
        Schema::dropIfExists('wish_service_details');
    }
}
