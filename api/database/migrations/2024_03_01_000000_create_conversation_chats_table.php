<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversation_chats', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('init_user_id')->nullable()->default(null);
            $table->bigInteger('end_user_id')->nullable()->default(null);
            $table->bigInteger('conversation_id')->nullable()->default(null);
            $table->string('chat_an_id', 300)->nullable()->default(null);
            $table->string('title', 2000)->nullable()->default(null);
            $table->text('content')->nullable()->default(null);
            $table->string('attachment', 2000)->nullable()->default(null);
            $table->string('op1', 2000)->nullable()->default(null);
            $table->string('op2', 2000)->nullable()->default(null);
            $table->string('seen_by_other_user', 50)->nullable()->default(false);
            $table->string('seen_by_received_user', 50)->nullable()->default(false);
            $table->dateTime('created_at')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);

            // Indexes
            ////$table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat');
    }
}
