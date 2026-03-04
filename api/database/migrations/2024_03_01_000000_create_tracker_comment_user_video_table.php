<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackerCommentUserVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracker_comment_user_video', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('video_id')->nullable()->default(null);
            $table->bigInteger('trade_item_id')->nullable()->default(null);
            $table->string('tcuvt_comment_an_id', 50)->nullable()->default(null);
            $table->string('tcuvt_video_an_id', 50)->nullable()->default(null);
            $table->string('tcuvt_user_an_id', 50)->nullable()->default(null);
            $table->string('tcuvt_up_vote', 15)->nullable()->default(0);
            $table->string('tcuvt_down_vote', 15)->nullable()->default(0);
            $table->dateTime('tcuvt_last_vote_date_change')->nullable()->default(null);
            $table->timestamp('tcuvt_record_insert_date')->nullable()->default(null);

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
        Schema::dropIfExists('tracker_comment_user_video');
    }
}
