<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesUserStoredTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files_user_stored', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->string('file_store_an_id', 300)->nullable()->default(null);
            $table->string('filename', 500)->nullable()->default(null);
            $table->string('foldername', 500)->nullable()->default(null);
            $table->string('status', 50)->nullable()->default('active');
            $table->string('verify_status', 50)->nullable()->default('inactive');
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
        Schema::dropIfExists('files_user_stored');
    }
}
