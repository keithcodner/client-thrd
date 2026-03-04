<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversation_categories', function (Blueprint $table) {
            $table->increments('id')->nullable(); // Unsigned integer, auto-increment by default
            $table->unsignedInteger('owner_user_id')->nullable();
            $table->string('category_an_id', 100)->nullable();
            $table->string('category_name', 255)->nullable();
            $table->text('category_description')->nullable(); // Text for long descriptions
            $table->text('category_expand_state')->nullable(); // Text for long descriptions
            $table->string('category_status', 50)->nullable();
            $table->string('category_type', 50)->nullable();
            $table->timestamps(); // Automatically adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversation_categories');
    }
}
