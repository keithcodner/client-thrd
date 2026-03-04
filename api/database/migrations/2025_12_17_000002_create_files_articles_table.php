<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reference_id');
            $table->string('table_reference_name', 255)->nullable();
            $table->string('file_store_an_id', 500)->nullable();
            $table->string('filename', 255)->nullable();
            $table->string('foldername', 255)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('verify_status', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->integer('file_order')->nullable();
            $table->timestamps();

            // Add indexes for better query performance
            $table->index('reference_id');
            $table->index('table_reference_name');
            $table->index('status');
            $table->index('type');
            $table->index('file_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files_articles');
    }
};
