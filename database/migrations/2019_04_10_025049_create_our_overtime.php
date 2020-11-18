<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOurOvertime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('our_overtime', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('description')->nullable();;
            $table->date('date')->nullable();;
            // $table->time('start_hour')->nullable();;
            // $table->time('end_hour')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('our_overtime');
    }
}
