<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOvertimeLineShift extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('our_overtime', function (Blueprint $table) {
            $table->bigInteger('our_process_id')->unsigned();
            $table->foreign('our_process_id')->references('id')->on('our_process');

            $table->bigInteger('our_lines_id')->unsigned();
            $table->foreign('our_lines_id')->references('id')->on('our_lines');

            $table->bigInteger('our_shifts_id')->unsigned();
            $table->foreign('our_shifts_id')->references('id')->on('our_shifts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('our_overtime', function (Blueprint $table) {
            $table->dropForeign('our_overtime_our_process_id_foreign');
            $table->dropForeign('our_overtime_our_lines_id_foreign');
            $table->dropForeign('our_overtime_our_shifts_id_foreign');
            
            $table->dropColumn('our_process_id');
            $table->dropColumn('our_lines_id');
            $table->dropColumn('our_shifts_id');
        });
    }
}
