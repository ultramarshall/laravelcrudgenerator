<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('ipaddress', 50)->nullable();
            $table->string('useragent')->nullable();
            $table->string('url')->nullable();
            $table->string('description')->nullable();
            $table->text('details')->nullable();
            $table->integer('mit_users_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mit_logs');
    }
}
