<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mit_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->integer('mit_users_id')->nullable();
            $table->string('content')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_read')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mit_notifications');
    }
}
