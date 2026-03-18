<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('circles_idea_board', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('circle_id')->nullable();
            $table->string('file_store_circle_an_id', 255)->nullable();
            $table->text('details')->nullable();
            $table->string('type', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('circle_id')->references('id')->on('circles')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('circles_idea_board');
    }
};