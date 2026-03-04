<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackerThumbVoteVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracker_thumb_vote_video', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('video_id')->nullable()->default(null);
            $table->string('tlut_up_vote', 15)->nullable()->default(0);
            $table->string('tlut_down_vote', 15)->nullable()->default(0);
            $table->dateTime('tlut_last_vote_date_change')->nullable()->default(null);
            $table->dateTime('tlut_record_insert_date')->nullable()->default(null);

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
        Schema::dropIfExists('tracker_thumb_vote_video');
    }
}
