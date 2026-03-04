<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('comments_id')->nullable()->default(null);
            $table->bigInteger('likes_id')->nullable()->default(null);
            $table->string('post_an_id', 300)->nullable()->default(null);
            $table->string('file_store_an_id', 200)->nullable()->default(null);
            $table->longText ('body')->nullable()->default(null);
            $table->string('status', 45)->nullable()->default('active');
            $table->string('type', 45)->nullable()->default('general');
            $table->string('isVisible', 45)->nullable()->default(true);
            $table->string('shareLink', 500)->nullable()->default(0);
            $table->bigInteger('views')->nullable()->default(null);
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
        Schema::dropIfExists('posts');
    }
}
