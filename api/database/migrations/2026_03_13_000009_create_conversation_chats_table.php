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
        Schema::create('conversation_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('init_user_id')->nullable();
            $table->unsignedBigInteger('end_user_id')->nullable();
            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->string('chat_an_id', 300)->nullable();
            $table->string('title', 2000)->nullable();
            $table->text('content');
            $table->string('attachment', 2000)->nullable();
            $table->string('op1', 2000)->nullable();
            $table->string('op2', 2000)->nullable();
            $table->string('seen_by_other_user', 50)->default('0');
            $table->string('seen_by_received_user', 50)->default('0');
            $table->string('type', 50)->default('0');
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
        Schema::dropIfExists('conversation_chats');
    }
};