<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationGroupTrackerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversation_group_tracker', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID column (primary key)
            $table->unsignedInteger('convo_cat_id')->nullable();
            $table->unsignedInteger('convo_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('an_id', 50)->nullable();
            $table->string('tracker_type', 50)->nullable();
            $table->string('tracker_status', 50)->nullable();
            $table->unsignedInteger('tracker_order')->nullable();
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversation_group_tracker');
    }
}
