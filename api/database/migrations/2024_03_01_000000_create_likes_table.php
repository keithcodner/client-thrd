<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('post_id')->nullable()->default(null);
            $table->bigInteger('comment_id')->nullable()->default(null);
            $table->bigInteger('item_id')->nullable()->default(null);
            $table->bigInteger('pronetwork_group_profile_id')->nullable()->default(null);
            $table->string('lk_status', 45)->nullable()->default('active');
            $table->string('lk_type', 45)->nullable()->default('like');
            $table->string('lk_value', 45)->nullable()->default(null);
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
        Schema::dropIfExists('likes');
    }
}
