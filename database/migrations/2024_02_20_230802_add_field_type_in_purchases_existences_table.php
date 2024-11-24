<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldTypeInPurchasesExistencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases_existences', function (Blueprint $table) {
            $table->integer('type')->after('id');
            // $table->renameColumn('raw_articulo_id', 'product_id');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases_existences', function (Blueprint $table) {
            $table->dropColumn('type');
            // $table->renameColumn('product_id','raw_articulo_id' );
        });
    }
}
