<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_content', function (Blueprint $table) {
            $table->bigInteger('id')->default(0)->primary(); // BIGINT(19) NOT NULL DEFAULT '0' PRIMARY KEY
            $table->string('an_id')->nullable(); // VARCHAR(255) NULL DEFAULT NULL
            $table->integer('order')->nullable(); // INT(10) NULL DEFAULT NULL
            $table->text('content1')->nullable(); // TEXT NULL DEFAULT NULL
            $table->text('content2')->nullable(); // TEXT NULL DEFAULT NULL
            $table->text('content3')->nullable(); // TEXT NULL DEFAULT NULL
            $table->string('type', 50)->nullable(); // VARCHAR(50) NULL DEFAULT NULL
            $table->string('status', 50)->nullable(); // VARCHAR(50) NULL DEFAULT NULL
            $table->dateTime('created_at')->nullable(); // DATETIME NULL DEFAULT NULL
            $table->dateTime('updated_at')->nullable(); // DATETIME NULL DEFAULT NULL
        });
    }

    public function down(): void
    {
    Schema::dropIfExists('event_content');
    }
};
