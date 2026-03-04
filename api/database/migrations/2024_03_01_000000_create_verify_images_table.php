<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifyImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verify_images', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('image_id')->nullable()->default(null);
            $table->integer('reviewer_user_id')->nullable()->default(null);
            $table->integer('reason_id')->nullable()->default(null);
            $table->string('reason_table', 255)->nullable()->default(null);
            $table->string('image_status_1', 255)->nullable()->default(null);
            $table->string('image_status_2', 255)->nullable()->default(null);
            $table->integer('validation_count')->nullable()->default(null);
            $table->integer('validation_threshold')->nullable()->default(null);
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
        Schema::dropIfExists('verify_images');
    }
}
