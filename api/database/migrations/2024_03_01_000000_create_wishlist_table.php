<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWishlistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wishlist', function (Blueprint $table) {
            $table->bigInteger('id')->default(0);
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->string('circle_item_type_id', 50)->nullable()->default(null);
            $table->bigInteger('parent_type')->nullable()->default(null);
            $table->bigInteger('file_stored_an_id')->nullable()->default(null);
            $table->string('name', 300)->nullable()->default(null);
            $table->string('description', 5000)->nullable()->default(null);
            $table->string('status', 50)->nullable()->default('active');
            $table->text('image_url')->nullable()->default(null);
            $table->text('image_filename')->nullable()->default(null);
            $table->dateTime('created_at')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);

            // Indexes
            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wishlist');
    }
}