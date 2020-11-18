<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mit_menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('name')->nullable();
            $table->string('type')->default('url');
            $table->string('path')->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->integer('parent_id')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_dashboard')->default(0);
            $table->integer('mit_privileges_id')->nullable();
            $table->integer('sorting')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mit_menus');
    }
}
