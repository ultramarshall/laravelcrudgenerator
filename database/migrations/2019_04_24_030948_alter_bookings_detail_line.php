<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBookingsDetailLine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('our_bookings_detail', function (Blueprint $table) {
            $table->bigInteger('our_lines_id')->unsigned()->nullable();
            $table->foreign('our_lines_id')->references('id')->on('our_lines')->onDelete('cascade')->onUpdate('cascade');

            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('our_bookings_detail', function (Blueprint $table) {
            $table->dropForeign('our_bookings_detail_our_lines_id_foreign');

            $table->dropColumn('our_lines_id');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });
    }
}
