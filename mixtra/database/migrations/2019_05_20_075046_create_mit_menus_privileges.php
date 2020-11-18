<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitMenusPrivileges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mit_menus_privileges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->integer('mit_menus_id')->nullable();
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
        Schema::dropIfExists('mit_menus_privileges');
    }
}
