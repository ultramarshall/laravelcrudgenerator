<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOurSpeeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('our_speeds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('approved_date');
            $table->datetime('start_date');
            $table->string('layouts');
            $table->integer('tipical_numerik');
            $table->unsignedInteger('our_items_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('our_speed');
    }
}
