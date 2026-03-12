<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('post_id')->nullable()->default(null);
            $table->bigInteger('circle_item_id')->nullable()->default(null);
            $table->bigInteger('video_id')->nullable()->default(null);
            $table->bigInteger('incident_id')->nullable()->default(null);
            $table->bigInteger('pronetwork_group_profile_id')->nullable()->default(null);
            $table->string('comm_an_id', 255)->nullable()->default(null);
            $table->string('comm_usr_an_id', 255)->nullable()->default(null);
            $table->string('comm_comment_unique_an_id', 255)->nullable()->default(null);
            $table->string('comm_comment')->nullable()->default(null);
            $table->string('comm_reply_an_id', 50)->nullable()->default(null);
            $table->string('comm_is_reply', 10)->nullable()->default('No');
            $table->string('comm_reply_parent_an_id', 255)->nullable()->default(null);
            $table->string('comm_dislike', 15)->nullable()->default(null);
            $table->string('comm_status', 50)->nullable()->default('Active');
            $table->string('comm_s_status', 50)->nullable()->default('unread');
            $table->string('comm_like', 15)->nullable()->default(null);
            $table->string('comm_type', 50)->nullable()->default(null);
            $table->string('comm_ui_is_public', 15)->nullable()->default(null);
            $table->string('comm_type_id', 30)->nullable()->default(null);
            $table->string('comm_ui_is_read', 10)->nullable()->default(null);
            $table->string('comm_name', 50)->nullable()->default(null);
            $table->string('comm_email', 200)->nullable()->default(null);
            $table->dateTime('created_at')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);

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
        Schema::dropIfExists('comments');
    }
}
