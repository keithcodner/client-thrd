<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranking_permissions', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('rank_perm_name', 355)->nullable()->default(null);
            $table->integer('rank_perm_threshold')->nullable()->default(null);
            $table->integer('rank_perm_value')->nullable()->default(null);
            $table->integer('rank_perm_order')->nullable()->default(null);
            $table->string('rank_perm_type1', 255)->nullable()->default(null);
            $table->string('rank_perm_type2', 255)->nullable()->default(null);
            $table->string('rank_perm_status', 255)->nullable()->default(null);
            $table->string('rank_perm_limit_duration', 500)->nullable()->default(null);
            $table->string('rank_perm_op2', 500)->nullable()->default(null);
            $table->string('rank_perm_op3', 500)->nullable()->default(null);
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
        Schema::dropIfExists('ranking_permissions');
    }
}
