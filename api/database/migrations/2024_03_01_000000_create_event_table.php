<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->integer('user_from_id')->nullable();
            $table->integer('user_to_id')->nullable();
            $table->integer('trade_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('post_id')->nullable();
            $table->integer('file_store_event_id')->nullable();
            $table->string('event_an_id'); // VARCHAR(255) NOT NULL
            $table->string('name'); // VARCHAR(255) NOT NULL
            $table->dateTime('event_date_time'); // DATETIME NOT NULL
            $table->dateTime('event_date_time_start_range')->nullable();
            $table->dateTime('event_date_time_end_range')->nullable();
            $table->text('description')->nullable();
            $table->string('type', 100)->nullable();
            $table->string('type_second', 100)->nullable();
            $table->string('status', 100)->nullable();
            $table->string('status_second', 100)->nullable();
            $table->string('link')->nullable();
            $table->string('isVisibleToOthers', 50)->nullable();
            $table->string('category', 50)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
