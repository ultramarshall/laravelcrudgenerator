<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOurItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('our_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('sku')->nullable();
            $table->string('item_no')->nullable();
            $table->string('description')->nullable();
            $table->string('color')->nullable();
            $table->string('kontruksi')->nullable();
            $table->string('production_type')->nullable();
            $table->string('profile')->nullable();
            $table->integer('conversion')->nullable();
            $table->integer('speed')->nullable();
            $table->integer('mp')->nullable();
            $table->decimal('cbm', 8, 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('our_items');
    }
}
