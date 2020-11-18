<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOurBookingsDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('our_bookings_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->integer('our_bookings_id')->nullable();
            $table->integer('our_items_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('item_no')->nullable();;
            $table->string('description')->nullable();;
            $table->integer('qty')->default(1);
            $table->decimal('speed',8,3)->default(0);
            $table->decimal('work_hour',8,3)->default(0);
            $table->decimal('mp',8,3)->default(0);
            $table->decimal('carton',8,3)->default(0);
            $table->decimal('cbm',8,3)->default(0);
            $table->decimal('cbm_qty',8,3)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('our_bookings_detail');
    }
}
