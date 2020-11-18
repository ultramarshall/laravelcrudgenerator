<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOurLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('our_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('name')->nullable();
            $table->string('background_color')->nullable();
            $table->string('text_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('our_lines');
    }
}
