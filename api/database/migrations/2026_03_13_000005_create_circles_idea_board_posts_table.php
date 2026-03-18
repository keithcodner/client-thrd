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
        Schema::create('circles_idea_board_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('circles_idea_board_id')->nullable();
            $table->unsignedBigInteger('user_owner_id')->nullable();
            $table->string('name', 500)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('circles_idea_board_id')->references('id')->on('circles_idea_board')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_owner_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('circles_idea_board_posts');
    }
};