<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBookingVoucher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('our_bookings', function (Blueprint $table) {
            $table->string('voucher_no',25)->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('our_bookings', function (Blueprint $table) {
            $table->dropColumn('voucher_no');            
        });
    }
}
