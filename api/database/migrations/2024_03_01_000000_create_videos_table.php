<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('trade_item_id')->nullable()->default(null);
            $table->string('video_reject_reason', 5000);
            $table->string('video_vid_comment_id', 50)->nullable()->default(null);
            $table->string('video_usr_id', 40)->nullable()->default(null);
            $table->string('video_external_id', 55)->nullable()->default(null);
            $table->string('video_title', 120)->nullable()->default(null);
            $table->text('video_description')->nullable()->default(null);
            $table->string('video_filename', 255)->nullable()->default(null);
            $table->string('video_file_path', 500)->nullable()->default(null);
            $table->string('video_web_id', 255)->nullable()->default(null);
            $table->string('video_category', 50)->nullable()->default(null);
            $table->string('video_size', 50)->nullable()->default(null);
            $table->string('video_hits', 15)->nullable()->default(0);
            $table->string('video_likes', 15)->nullable()->default(0);
            $table->string('video_dislikes', 15)->nullable()->default(0);
            $table->string('video_shares', 15)->nullable()->default(0);
            $table->string('video_tags', 255)->nullable()->default(null);
            $table->string('video_status', 50)->nullable()->default('Active');
            $table->string('video_access', 50)->nullable()->default('Public');
            $table->string('video_privacy', 50)->nullable()->default('None');
            $table->string('video_inStore', 50)->nullable()->default('No');
            $table->string('video_price', 50)->nullable()->default('$0.00');
            $table->string('video_type', 50)->nullable()->default('codr license');
            $table->string('video_isSeries', 50)->nullable()->default(null);
            $table->string('video_series_id', 50)->nullable()->default(null);
            $table->string('video_series_num', 50)->nullable()->default(null);
            $table->string('video_series_name', 255)->nullable()->default(null);
            $table->timestamp('video_datecreated')->nullable()->default(null);
            $table->string('video_thumbnail_name', 500)->nullable()->default(null);
            $table->string('video_duration', 15)->nullable()->default('00:00');
            $table->string('video_resolution_x', 15)->nullable()->default();
            $table->string('video_resolution_y', 15)->nullable()->default();
            $table->string('video_bitrate', 15)->nullable()->default();
            $table->string('video_allow_comments', 15)->nullable()->default('Yes');
            $table->string('video_approval', 20)->default('Not Approved');
            $table->string('video_is_this_refuted', 255)->nullable()->default(null);
            $table->dateTime('video_rejection_date');
            $table->string('video_claim_dispute_reason', 500);
            $table->dateTime('video_successful_or_failure_refute_date');
            $table->string('video_final_dispute_statement', 1000);

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
        Schema::dropIfExists('videos');
    }
}
