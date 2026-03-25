<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('files_event', function (Blueprint $table) {
            $table->id(); // INT AUTO_INCREMENT PRIMARY KEY
            $table->integer('reference_id'); // INT NOT NULL
            $table->string('table_reference_name')->nullable(); // VARCHAR(255) NULL
            $table->string('file_store_an_id', 500)->nullable(); // VARCHAR(500) NULL
            $table->string('filename')->nullable(); // VARCHAR(255) NULL
            $table->string('foldername')->nullable(); // VARCHAR(255) NULL
            $table->string('status', 50)->nullable(); // VARCHAR(50) NULL
            $table->string('verify_status', 50)->nullable(); // VARCHAR(50) NULL
            $table->string('type', 50)->nullable(); // VARCHAR(50) NULL
            $table->integer('file_order')->nullable(); // INT NULL
            $table->dateTime('created_at')->nullable(); // DATETIME NULL
            $table->dateTime('updated_at')->nullable(); // DATETIME NULL
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files_event');
    }
};
