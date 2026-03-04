<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranking_groups', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('rank_group_type', 300)->nullable()->default(null);
            $table->string('rank_group_tier', 500)->nullable()->default(null);
            $table->integer('rank_group_order')->nullable()->default(null);
            $table->double('rank_group_weighted_score_threshold')->nullable()->default(null);
            $table->string('rank_group_status', 255)->nullable()->default(null);
            $table->string('rank_group_data', 255)->nullable()->default(null);
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
        Schema::dropIfExists('ranking_groups');
    }
}
