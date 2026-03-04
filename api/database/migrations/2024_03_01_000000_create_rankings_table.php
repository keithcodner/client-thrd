<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->index()->nullable()->default(null);
            $table->bigInteger('rank_group_id')->index()->nullable()->default(null);
            $table->bigInteger('rank_weight_id')->index()->nullable()->default(null);
            $table->string('rank_status', 200)->nullable()->default(null);
            $table->double('rank_score')->nullable()->default(null);
            $table->double('rank_weighed_score')->nullable()->default(null);
            $table->string('rank_data', 5000)->nullable()->default(null);
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
        Schema::dropIfExists('rankings');
    }
}
