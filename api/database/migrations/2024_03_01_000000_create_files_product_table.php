<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesProductTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('reference_id');
            $table->string('table_reference_name')->nullable();
            $table->string('file_store_an_id', 500)->nullable();
            $table->string('filename')->nullable();
            $table->string('foldername')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('verify_status', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->unsignedInteger('file_order')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files_product');
    }
}
