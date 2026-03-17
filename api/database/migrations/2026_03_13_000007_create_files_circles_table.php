<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files_circles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reference_id');
            $table->string('table_reference_name', 255)->nullable();
            $table->string('file_store_an_id', 500)->nullable();
            $table->string('filename', 255)->nullable();
            $table->string('foldername', 255)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('verify_status', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->integer('file_order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files_circles');
    }
};