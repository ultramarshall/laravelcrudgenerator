<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitEmailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mit_email_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('subject')->nullable();
            $table->longtext('content')->nullable();
            $table->string('description')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('cc_email')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mit_email_templates');
    }
}
