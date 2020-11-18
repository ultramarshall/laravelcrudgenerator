<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitPrivilegesRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mit_privileges_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->boolean('is_visible')->nullable();
            $table->boolean('is_create')->nullable();
            $table->boolean('is_read')->nullable();
            $table->boolean('is_edit')->nullable();
            $table->boolean('is_delete')->nullable();
            $table->integer('mit_privileges_id')->nullable();
            $table->integer('mit_modules_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mit_privileges_roles');
    }
}
