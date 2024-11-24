<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldConstructionSiteIdInWishServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wish_services', function (Blueprint $table) {
            $table->unsignedInteger('construction_site_id')->after('user_id');
            $table->foreign('construction_site_id')->references('id')->on('construction_site');
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
