<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterItemsLine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('our_items', function (Blueprint $table) {
            $table->bigInteger('our_lines_id')->unsigned()->nullable();
            $table->foreign('our_lines_id')->references('id')->on('our_lines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('our_items', function (Blueprint $table) {
            $table->dropForeign('our_items_our_lines_id_foreign');

            $table->dropColumn('our_lines_id');
        });
    }
}
