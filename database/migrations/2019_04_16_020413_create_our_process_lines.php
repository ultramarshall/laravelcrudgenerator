<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOurProcessLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('our_process_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->bigInteger('our_process_id')->unsigned();
            $table->foreign('our_process_id')->references('id')->on('our_process')->onDelete('cascade');

            $table->bigInteger('our_lines_id')->unsigned();
            $table->foreign('our_lines_id')->references('id')->on('our_lines')->onDelete('cascade');

            $table->bigInteger('our_shifts_id')->unsigned();
            $table->foreign('our_shifts_id')->references('id')->on('our_shifts')->onDelete('cascade');

            $table->string('name')->nullable();
            $table->date('start_date')->nullable();
            $table->boolean('is_active')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('our_process_lines');
    }
}
