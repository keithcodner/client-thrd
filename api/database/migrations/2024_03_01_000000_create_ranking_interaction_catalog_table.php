<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingInteractionCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranking_interaction_catalog', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('rank_interact_name', 1000)->nullable()->default(null);
            $table->double('rank_interact_rate')->nullable()->default(null);
            $table->string('rank_interact_type', 300)->nullable()->default(null);
            $table->string('rank_interact_status', 300)->nullable()->default(null);
            $table->integer('rank_interact_passive_threshold')->nullable()->default(null);
            $table->integer('rank_interact_passive_reward')->nullable()->default(null);
            $table->string('rank_interact_op_1', 500)->nullable()->default(null);
            $table->string('rank_interact_op_2', 500)->nullable()->default(null);
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
        Schema::dropIfExists('ranking_interaction_catalog');
    }
}
