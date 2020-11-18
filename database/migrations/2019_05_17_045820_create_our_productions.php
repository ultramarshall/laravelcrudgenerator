<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOurProductions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('our_productions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->bigInteger('our_bookings_detail_id')->unsigned();
            $table->foreign('our_bookings_detail_id')->references('id')->on('our_bookings_detail')->onDelete('cascade');

            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->string('reason')->nullable();
            $table->integer('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('our_productions');
    }
}
