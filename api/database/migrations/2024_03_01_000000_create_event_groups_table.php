<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_groups', function (Blueprint $table) {
            $table->id(); // INT AUTO_INCREMENT PRIMARY KEY
            $table->integer('event_id')->nullable(); // INT NULL DEFAULT NULL - Allow null initially
            $table->integer('user_id')->nullable(); // INT NULL DEFAULT NULL
            $table->string('group_name')->default(''); // VARCHAR(255) NULL DEFAULT ''
            $table->string('type', 50)->default(''); // VARCHAR(50) NULL DEFAULT ''
            $table->string('status', 50)->default(''); // VARCHAR(50) NULL DEFAULT ''
            $table->dateTime('created_at'); // DATETIME NOT NULL
            $table->dateTime('updated_at')->nullable(); // DATETIME NULL DEFAULT NULL
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_groups');
    }
};
