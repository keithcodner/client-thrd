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
        Schema::create('circles_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('circle_id')->nullable();
            $table->unsignedInteger('circle_idea_board_id')->nullable();
            $table->string('file_store_circle_an_id', 250)->nullable();
            $table->string('file_store_circle_bg_img_an_id', 250)->nullable();
            $table->string('description', 5000)->nullable();
            $table->string('transparency_percent', 50)->nullable();
            $table->string('blur_depth_value', 50)->nullable();
            $table->string('style_code', 50)->nullable();
            $table->string('notification_code', 50)->nullable();
            $table->string('privacy_state', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('circle_id')->references('id')->on('circles')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('circle_idea_board_id')->references('id')->on('circles_idea_board')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('circles_details');
    }
};