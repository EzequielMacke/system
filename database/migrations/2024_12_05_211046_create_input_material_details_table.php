<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInputMaterialDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('input_material_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('input_id');
            $table->foreign('input_id')->references('id')->on('inputs');
            $table->unsignedInteger('material_id');
            $table->foreign('material_id')->references('id')->on('materials');
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
        Schema::dropIfExists('input_material_details');
    }
}
