<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingTransactionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranking_transaction_history', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('rank_trans_an_id', 300)->index()->nullable()->default(null);
            $table->bigInteger('rank_interact_id')->index()->nullable()->default(null);
            $table->bigInteger('rank_id')->nullable()->default(null);
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('reason_id')->nullable()->default(null);
            $table->string('reason_table', 255)->nullable()->default(null);
            $table->string('rank_trans_reason_desc', 5000)->nullable()->default(null);
            $table->string('rank_trans_initiator', 100)->nullable()->default(null);
            $table->string('rank_trans_name', 300)->nullable()->default(null);
            $table->string('rank_trans_type', 100)->nullable()->default(null);
            $table->string('rank_trans_group', 100)->nullable()->default(null);
            $table->double('rank_trans_start_rank')->nullable()->default(null);
            $table->string('rank_trans_direction', 100)->nullable()->default(null);
            $table->double('rank_trans_amount')->nullable()->default(null);
            $table->double('rank_trans_end_rank')->nullable()->default(null);
            $table->string('rank_trans_status', 50)->nullable()->default(null);
            $table->integer('rank_trans_threshold')->nullable()->default(null);
            $table->integer('rank_trans_threshold_count')->nullable()->default(null);
            $table->string('rank_trans_trigger', 500)->nullable()->default(null);
            $table->string('rank_trans_data', 2000)->nullable()->default(null);
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
        Schema::dropIfExists('ranking_transaction_history');
    }
}
