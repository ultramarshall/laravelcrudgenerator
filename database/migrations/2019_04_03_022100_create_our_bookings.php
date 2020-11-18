<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOurBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('our_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->integer('our_customers_id')->nullable();
            $table->integer('our_retailers_id')->nullable();
            $table->string('type')->nullable();
            $table->string('customer_po')->nullable();
            $table->string('retailer_po')->nullable();
            $table->string('status')->nullable();
            $table->datetime('inquiry_date')->nullable();
            $table->datetime('ship_date')->nullable();
            $table->decimal('work_hour', 8, 3)->nullable();
            $table->integer('lead_time')->nullable();
            $table->decimal('cbm', 8, 3)->nullable();
            $table->string('container_load')->nullable();
            $table->datetime('actual_mrp')->nullable();
            $table->datetime('purchase_date')->nullable();
            $table->boolean('inspection')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('our_bookings');
    }
}
