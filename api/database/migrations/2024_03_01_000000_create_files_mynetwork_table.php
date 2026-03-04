<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesMynetworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files_mynetwork', function (Blueprint $table) {
            $table->id(); // Primary key: id (auto-increment)
            $table->unsignedBigInteger('reference_id'); // Reference ID, INT NOT NULL
            $table->string('table_reference_name', 255)->nullable(); // Table reference name, VARCHAR(255), nullable
            $table->unsignedBigInteger('file_store_an_id')->nullable(); // File store ID, INT, nullable
            $table->string('filename', 255)->nullable(); // Filename, VARCHAR(255), nullable
            $table->string('foldername', 255)->nullable(); // Folder name, VARCHAR(255), nullable
            $table->string('status', 50)->nullable(); // Status, VARCHAR(50), nullable
            $table->string('verify_status', 50)->nullable(); // Verify status, VARCHAR(50), nullable
            $table->string('type', 50)->nullable(); // Type, VARCHAR(50), nullable
            $table->integer('file_order')->nullable(); // File order, INT, nullable
            $table->dateTime('created_at')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files_mynetwork');
    }
}
