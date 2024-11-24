<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldBranchIdInWishServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wish_services', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->after('status')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->string('observation')->after('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wish_services', function (Blueprint $table) {
            //
        });
    }
}
