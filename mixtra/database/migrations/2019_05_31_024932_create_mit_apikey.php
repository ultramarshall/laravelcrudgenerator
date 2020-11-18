<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitApikey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mit_apikey', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('screetkey')->nullable();
            $table->integer('hit')->nullable();
            $table->string('status', 25)->default('active');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mit_apikey');
    }
}
