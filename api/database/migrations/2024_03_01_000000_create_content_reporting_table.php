<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentReportingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_reporting', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('reason_id')->nullable()->default(null);
            $table->bigInteger('incident_id')->nullable()->default(null);
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->string('reporting_an_id', 300)->nullable()->default(null);
            $table->string('reason_table', 255)->nullable()->default(null);
            $table->string('reporting_type', 255)->nullable()->default(null);
            $table->string('reporting_title', 255)->nullable()->default(null);
            $table->longText ('reporting_description')->nullable()->default(null);
            $table->string('reporting_status')->nullable()->default(null);
            $table->string('reporting_threshold', 50)->nullable()->default(null);
            $table->string('reporting_threshold_count', 50)->nullable()->default(null);
            $table->string('reporting_isAppealed', 50)->nullable()->default('no');
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
        Schema::dropIfExists('content_reporting');
    }
}
