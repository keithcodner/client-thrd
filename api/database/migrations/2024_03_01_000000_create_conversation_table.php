<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            

            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('from_id')->nullable()->default(null);
            $table->bigInteger('item_id')->nullable()->default(null);
            $table->string('group_ids', 500)->nullable()->default(null);
            $table->string('conv_an_id', 300)->nullable()->default(null);
            $table->string('title', 2000)->nullable()->default(null);
            $table->text('content')->nullable()->default(null);
            $table->string('deleted_by_user_id', 100)->nullable()->default(null);
            $table->string('deleted_by_from_id', 100)->nullable()->default(null);
            $table->string('deleted_by_group_ids', 100)->nullable()->default(null);
            $table->string('status', 2000)->nullable()->default('active');
            $table->string('status_second', 2000)->nullable()->default(null);
            $table->string('type', 100)->nullable()->default('couple');
            $table->string('type_second', 100)->nullable()->default('couple');
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
        // Schema::table('conversations', function (Blueprint $table) {
    
           

        //     $table->dropForeign(['user_id']);

        //     $table->dropForeign(['from_id']);
        // });

        Schema::dropIfExists('conversations');
    }
}
