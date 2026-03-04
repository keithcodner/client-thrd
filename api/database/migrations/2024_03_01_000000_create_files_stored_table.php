<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesStoredTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files_stored', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('trade_item_post_id')->nullable()->default(null);
            $table->bigInteger('wishlist_item_id')->nullable()->default(null);
            $table->string('file_store_an_id', 100)->index()->nullable()->default(null);
            $table->string('file_store_wishlist_an_id', 100)->index()->nullable()->default(null);
            $table->string('filename', 500)->nullable()->default(null);
            $table->string('foldername', 500)->nullable()->default(null);
            $table->string('status', 50)->nullable()->default('active');
            $table->string('verify_status', 50)->nullable()->default('unverified');
            $table->string('type', 50)->nullable()->default('image');
            $table->string('order', 50)->nullable()->default(null);
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
        Schema::dropIfExists('files_stored');
    }
}
