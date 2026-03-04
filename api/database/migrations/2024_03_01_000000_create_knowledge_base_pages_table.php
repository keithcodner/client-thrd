<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKnowledgeBasePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knowledge_base_pages', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('page_an_id', 300)->nullable()->default(null);
            $table->string('page_name', 255)->nullable()->default(null);
            $table->string('link_name', 255)->nullable()->default(null);
            $table->integer('page_creator_user_id')->nullable()->default(null);
            $table->string('description', 5000)->nullable()->default(null);
            $table->string('status', 255)->nullable()->default(null);
            $table->string('type', 255)->nullable()->default(null);
            $table->dateTime('created_at')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);

            // Indexes
            //$table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('knowledge_base_pages');
    }
}
