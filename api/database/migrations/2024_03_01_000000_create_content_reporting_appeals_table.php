<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentReportingAppealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_reporting_appeals', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('content_report_id')->nullable()->default(null);
            $table->longText ('report_appeals_reason_desc')->nullable()->default(null);
            $table->longText ('report_appeals_response_desc')->nullable()->default(null);
            $table->string('report_appeals_decision', 200)->nullable()->default(null);
            $table->string('report_appeals_status', 255)->nullable()->default(null);
            $table->string('report_appeals_second_status', 255)->nullable()->default(null);
            $table->string('op_1', 5000)->nullable()->default(null);
            $table->string('op_2', 5000)->nullable()->default(null);
            $table->string('op_3', 5000)->nullable()->default(null);
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
        Schema::dropIfExists('content_reporting_appeals');
    }
}
