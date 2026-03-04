<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePronetworkUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pronetwork_user_profile', function (Blueprint $table) {
            $table->id(); // Creates an auto-incrementing UNSIGNED BIGINT primary key
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('header_image_id')->nullable();
            $table->unsignedBigInteger('profile_image_id')->nullable();
            $table->string('general_headline', 255)->nullable();
            $table->text('detailed_about')->nullable();
            $table->string('general_location_city', 255)->nullable();
            $table->string('general_location_country', 255)->nullable();
            $table->string('general_location_state_province', 255)->nullable();
            $table->string('general_trade', 255)->nullable();
            $table->string('general_profession', 255)->nullable();
            $table->string('website_link', 1000)->nullable();
            $table->string('social_media_link1', 500)->nullable();
            $table->string('social_media_link2', 500)->nullable();
            $table->string('social_media_link3', 500)->nullable();
            $table->string('social_media_link4', 500)->nullable();
            $table->string('social_media_link5', 500)->nullable();
            $table->string('social_media_link6', 500)->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('connections_count')->default(0);
            $table->string('contact_email', 255)->nullable();
            $table->text('general_skills')->nullable();
            $table->string('type', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->dateTime('created_at')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pronetwork_user_profile');
    }
}
