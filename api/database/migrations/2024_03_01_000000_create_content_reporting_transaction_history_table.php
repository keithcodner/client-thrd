<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentReportingTransactionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_reporting_transaction_history', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_reporting_id')->default(0);
            $table->bigInteger('reason_id')->nullable()->default(null);
            $table->string('reason_table', 255)->default(0);
            $table->string('report_trans_type', 255)->nullable()->default(null);
            $table->string('report_trans_reason_desc', 5000)->nullable()->default(null);
            $table->string('report_trans_status', 50)->nullable()->default(null);
            $table->string('report_trans_data', 500)->default(0);
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
        Schema::dropIfExists('content_reporting_transaction_history');
    }
}
