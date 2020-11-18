<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRetaillerCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('our_retailers', function (Blueprint $table) {
            $table->bigInteger('our_customers_id')->unsigned();
            $table->foreign('our_customers_id')->references('id')->on('our_customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('our_retailers', function (Blueprint $table) {
            $table->dropForeign('our_retailers_our_customers_id_foreign');
            
            $table->dropColumn('our_customers_id');
        });
    }
}
