<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesCredentialsStoredTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files_credentials_stored', function (Blueprint $table) {
            $table->bigInteger('id')->default(0);
            $table->bigInteger('user_id')->default(0);
            $table->string('file_store_an_id', 500)->default(0);
            $table->string('filename', 255)->default(0);
            $table->string('foldername', 255)->default(0);
            $table->string('status', 255)->default('active');
            $table->string('verify_status', 255)->default('inactive');
            $table->string('type', 255)->default(0);
            $table->string('order', 255)->default(0);
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
        Schema::dropIfExists('files_credentials_stored');
    }
}
