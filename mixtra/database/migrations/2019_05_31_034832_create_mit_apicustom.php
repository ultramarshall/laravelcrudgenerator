<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitApicustom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mit_apicustom', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('permalink')->nullable();
            $table->string('table_name')->nullable();
            $table->string('action')->nullable();
            $table->text('columns')->nullable();
            $table->string('sorting')->nullable();
            $table->text('subquery')->nullable();
            $table->text('condition')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('method_type')->nullable();
            $table->longText('parameters')->nullable();
            $table->longText('responses')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mit_apicustom');
    }
}
