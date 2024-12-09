<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInputUsedDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('input_used_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('input_used_id');
            $table->foreign('input_used_id')->references('id')->on('input_useds');
            $table->unsignedInteger('input_id');
            $table->foreign('input_id')->references('id')->on('inputs');
            $table->integer('input_quantity');
            $table->unsignedInteger('id_material');
            $table->foreign('id_material')->references('id')->on('materials');
            $table->integer('material_quantity');
            $table->integer('measurement');
            $table->integer('total_quantity');
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
        Schema::dropIfExists('input_used_details');
    }
}
