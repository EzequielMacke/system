<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFielUserIdInInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inputs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('typeofservice')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('status')->after('typeofservice');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inputs', function (Blueprint $table) {
            //
        });
    }
}
