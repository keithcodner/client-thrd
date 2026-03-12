<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('from_id')->nullable()->default(null);
            $table->string('fk_circle_item_post_id', 255)->nullable()->default(null);
            $table->string('fk_conversation_id', 255)->nullable()->default(null);
            $table->string('fk_rankings_id', 255)->nullable()->default(null);
            $table->string('fk_ranking_transaction_history_id', 255)->nullable()->default(null);
            $table->string('fk_pronetwork_requests_id', 255)->nullable()->default(null);
            $table->string('fk_comments_id', 255)->nullable()->default(null);
            $table->string('fk_trxn_payment_transaction_id', 255)->nullable()->default(null);
            $table->string('fk_circle_transaction_id', 255)->nullable()->default(null);
            $table->string('fk_verify_images_id', 255)->nullable()->default(null);
            $table->string('notif_an_id', 300)->nullable()->default(null);
            $table->string('type', 255)->nullable()->default(null);
            $table->string('title', 2000)->nullable()->default(null);
            $table->longText ('comment')->nullable()->default(null);
            $table->longText ('note_table_name_target')->nullable()->default(null);
            $table->longText ('note_table_related_id')->nullable()->default(null);
            $table->longText ('note_relation_description')->nullable()->default(null);
            $table->longText ('op_1')->nullable()->default(null);
            $table->longText ('op_2')->nullable()->default(null);
            $table->longText ('op_3')->nullable()->default(null);
            $table->string('status', 50)->nullable()->default('new');
            $table->string('color_status', 50)->nullable()->default('black');
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
        Schema::dropIfExists('notifications');
    }
}
