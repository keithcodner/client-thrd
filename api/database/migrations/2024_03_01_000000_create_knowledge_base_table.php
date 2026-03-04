<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKnowledgeBaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knowledge_base', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('user_added_id')->nullable()->default(null);
            $table->integer('kb_parent_id')->nullable()->default(null);
            $table->string('kb_an_id', 255)->nullable()->default(null);
            $table->string('kb_type1', 255)->nullable()->default(null);
            $table->string('kb_type2', 255)->nullable()->default(null);
            $table->string('kb_summary', 1000)->nullable()->default(null);
            $table->text('kb_description')->nullable()->default(null);
            $table->string('kb_status', 50)->nullable()->default(null);
            $table->string('kb_views', 50)->nullable()->default(null);
            $table->string('kb_data', 50)->nullable()->default(null);
            $table->integer('kb_order')->nullable()->default(null);
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
        Schema::dropIfExists('knowledge_base');
    }
}
