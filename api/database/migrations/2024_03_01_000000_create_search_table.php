<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->string('search_text', 255)->nullable()->default(null);
            $table->string('ip', 255)->nullable()->default(null);
            $table->string('type', 30)->nullable()->default(null);
            $table->string('status', 90)->nullable()->default(null);
            $table->string('page', 255)->nullable()->default(null);
            $table->string('ttl', 50)->nullable()->default(null);
            $table->string('result_num', 30)->nullable()->default(null);
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
        Schema::dropIfExists('search');
    }
}
