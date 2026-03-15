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
            $table->id();
            $table->unsignedBigInteger('circle_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('circle_id')->references('id')->on('circles')->onDelete('cascade');
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