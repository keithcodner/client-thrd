<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackerSeriesVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracker_series_video', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->string('series_user_an_id', 50);
            $table->string('series_an_id', 50);
            $table->text('series_description');
            $table->longText ('series_video_data');
            $table->dateTime('series_date_last_mod');
            $table->dateTime('series_date_created');
            $table->string('series_status', 15)->default('Active');
            $table->string('series_access', 15)->default('Public');

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
        Schema::dropIfExists('tracker_series_video');
    }
}
