<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mit_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('name')->nullable();
            $table->string('photo')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->integer('mit_privileges_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mit_users');
    }
}
