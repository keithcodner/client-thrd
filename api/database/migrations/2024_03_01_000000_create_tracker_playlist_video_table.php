<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackerPlaylistVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracker_playlist_video', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->string('plylst_name', 255);
            $table->string('plylst_an_id', 50);
            $table->text('plylst_description');
            $table->longText ('plylst_video_data');
            $table->dateTime('plylst_date_last_mod');
            $table->dateTime('plylst_date_created');
            $table->string('plylst_status', 15)->default('Active');
            $table->string('plylst_access', 15)->default('Public');

            // Indexes
            //$table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracker_playlist_video');
    }
}
