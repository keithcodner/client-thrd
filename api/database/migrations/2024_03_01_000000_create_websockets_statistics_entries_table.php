<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsocketsStatisticsEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('websockets_statistics_entries', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('app_id', 255);
            $table->integer('peak_connection_count');
            $table->integer('websocket_message_count');
            $table->integer('api_message_count');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);

            // Indexes
            //$table->primary(['id']);
        });

        
        // ***** ADD FOREIGN KEYS - AFTER ALL TABLES CREATED *****
        Schema::table('address', function (Blueprint $table) {
    
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('chat', function (Blueprint $table) {
    
            $table->foreign('conversation_id')
                ->references('id')
                ->on('conversations')
                ->onDelete('cascade');

            $table->foreign('end_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('init_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('conversations', function (Blueprint $table) {
    
            $table->foreign('item_id')
                ->references('id')
                ->on('trade_item_post')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('from_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        // Schema::table('data_rows', function (Blueprint $table) {
    
        //     $table->foreign('data_type_id')
        //         ->references('id')
        //         ->on('data_types')
        //         ->onUpdate('cascade')
        //         ->onDelete('cascade');
        // });

        Schema::table('files_post_stored', function (Blueprint $table) {
    
            $table->foreign('post_id')
                ->references('id')
                ->on('posts')
                ->onDelete('cascade');
        });

        Schema::table('files_stored', function (Blueprint $table) {
    
            $table->foreign('file_store_an_id')
                ->references('file_stored_an_id')
                ->on('trade_item_post')
                ->onDelete('cascade');

            $table->foreign('trade_item_post_id')
                ->references('id')
                ->on('trade_item_post')
                ->onDelete('cascade');
        });

        Schema::table('files_user_stored', function (Blueprint $table) {
    
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('likes', function (Blueprint $table) {
    
            $table->foreign('comment_id')
                ->references('id')
                ->on('comments')
                ->onDelete('cascade');

            $table->foreign('post_id')
                ->references('id')
                ->on('posts')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('notifications', function (Blueprint $table) {
    
            $table->foreign('from_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
    
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('order_details', function (Blueprint $table) {
    
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
        });

        // Schema::table('permission_role', function (Blueprint $table) {
    
        //     $table->foreign('permission_id')
        //         ->references('id')
        //         ->on('permissions')
        //         ->onDelete('cascade');

        //     $table->foreign('role_id')
        //         ->references('id')
        //         ->on('roles')
        //         ->onDelete('cascade');
        // });

        Schema::table('posts', function (Blueprint $table) {
    
            $table->foreign('comments_id')
                ->references('id')
                ->on('comments')
                ->onDelete('cascade');

            $table->foreign('likes_id')
                ->references('id')
                ->on('likes')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
    
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('rankings', function (Blueprint $table) {
    
            $table->foreign('rank_group_id')
                ->references('id')
                ->on('ranking_groups')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
    
            $table->foreign('subbee')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('subber')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('trade_item_post', function (Blueprint $table) {
    
            $table->foreign('trade_item_type_id')
                ->references('id')
                ->on('trade_item_type')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('trade_transaction', function (Blueprint $table) {
    
            $table->foreign('trade_conversation_id')
                ->references('id')
                ->on('conversations')
                ->onDelete('cascade');

            $table->foreign('trade_id_prospect_item')
                ->references('id')
                ->on('trade_item_post')
                ->onDelete('cascade');

            $table->foreign('trade_id_initiator_item')
                ->references('id')
                ->on('trade_item_post')
                ->onDelete('cascade');

            $table->foreign('trade_id_initiator')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('trade_id_prospect')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('trade_transaction_history', function (Blueprint $table) {
    
            $table->foreign('trade_trans_id')
                ->references('id')
                ->on('trade_transaction')
                ->onDelete('cascade');
        });

        Schema::table('trxn_address_billing', function (Blueprint $table) {
    
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('trxn_address_shipping', function (Blueprint $table) {
    
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
    
            // $table->foreign('role_id')
            //     ->references('id')
            //     ->on('roles')
            //     ->onDelete('cascade');
        });

        Schema::table('user_roles', function (Blueprint $table) {
    
            // $table->foreign('role_id')
            //     ->references('id')
            //     ->on('roles')
            //     ->onDelete('cascade');

            // $table->foreign('user_id')
            //     ->references('id')
            //     ->on('users')
            //     ->onDelete('cascade');
        });

        Schema::table('videos', function (Blueprint $table) {
    
            $table->foreign('trade_item_id')
                ->references('id')
                ->on('trade_item_post')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });


        Schema::table('trade_transaction_insight', function (Blueprint $table) {
            $table->foreign('insight_campaign_answer_id')
                ->references('id')
                ->on('trade_transaction_insight_campaign_questions')
                ->onDelete('cascade');

            $table->foreign('insight_type_id')
                ->references('id')
                ->on('trade_transaction_insight_type')
                ->onDelete('cascade');
        });

        Schema::table('trade_transaction_insight_campaign_answers', function (Blueprint $table) {
            $table->foreign('giving_insight_user_id', 'fk_insight_campaign_answers_giving')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('trade_id', 'fk_insight_campaign_answers_trade')
                ->references('id')
                ->on('trade_transaction')
                ->onDelete('cascade');

            $table->foreign('insight_campaign_question_id', 'fk_insight_campaign_answers_question')
                ->references('id')
                ->on('trade_transaction_insight_campaign_questions')
                ->onDelete('cascade');
        });

        Schema::table('trade_transaction_insight_content', function (Blueprint $table) {
            $table->foreign('campaign_question_id', 'fk_insight_content_question')
                ->references('id')
                ->on('trade_transaction_insight_campaign_questions')
                ->onDelete('cascade');

            $table->foreign('insight_type_id', 'fk_insight_content_insight_type')
                ->references('id')
                ->on('trade_transaction_insight_type')
                ->onDelete('cascade');
        });

        Schema::table('trade_transaction_insight_answer_options', function (Blueprint $table) {
            $table->foreign('question_content_id', 'fk_insight_content_answer_options')
                ->references('id')
                ->on('trade_transaction_insight_content')
                ->onDelete('cascade');
        });

        Schema::table('trade_transaction_review', function (Blueprint $table) {
            $table->foreign('trade_transaction_id', 'FK_trade_review_transaction')
                ->references('id')
                ->on('trade_transaction')
                ->onDelete('cascade');

            $table->foreign('item_id', 'FK_trade_transaction_review_trade_item_post')
                ->references('id')
                ->on('trade_item_post')
                ->onDelete('cascade');
        });

        Schema::table('trxn_shopping_cart', function (Blueprint $table) {

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        // ***** TEAR DOWN FOREIGN KEYS - AFTER ALL TABLES CREATED *****
        // Schema::table('address', function (Blueprint $table) {
    
        //     $table->dropForeign(['user_id']);
        // });
        

        Schema::table('conversations', function (Blueprint $table) {
    
            $table->dropForeign(['item_id']);
        });

        Schema::table('chat', function (Blueprint $table) {
    
            $table->dropForeign(['conversation_id']);
        });

        // Schema::table('data_rows', function (Blueprint $table) {
    
            
        //     $table->dropForeign(['data_type_id']);
        // });

        Schema::table('files_post_stored', function (Blueprint $table) {
    
            $table->dropForeign(['post_id']);
            
        });

        Schema::table('files_stored', function (Blueprint $table) {
    
            $table->dropForeign(['file_store_an_id']);
            $table->dropForeign(['trade_item_post_id']);
        });

        Schema::table('files_user_stored', function (Blueprint $table) {
    
            $table->dropForeign(['user_id']);
        });

        Schema::table('likes', function (Blueprint $table) {
    
            $table->dropForeign(['item_id']);

            $table->dropForeign(['comment_id']);

            $table->dropForeign(['post_id']);

            $table->dropForeign(['user_id']);

            
        });

        Schema::table('notifications', function (Blueprint $table) {
    
            $table->dropForeign(['from_id']);

            $table->dropForeign(['user_id']);
        });

        Schema::table('order_details', function (Blueprint $table) {
    
            $table->dropForeign(['order_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
    
            $table->dropForeign(['user_id']);
        });

        Schema::table('permission_role', function (Blueprint $table) {
    
            $table->dropForeign(['permission_id']);

            $table->dropForeign(['role_id']);
        });

        Schema::table('posts', function (Blueprint $table) {
    
            $table->dropForeign(['comments_id']);

            $table->dropForeign(['likes_id']);

            $table->dropForeign(['user_id']);
        });

        Schema::table('products', function (Blueprint $table) {
    
            $table->dropForeign(['user_id']);
        });

        Schema::table('rankings', function (Blueprint $table) {
    
            //$table->dropForeign(['rankings_ranking_groups_foreign']);

        });

        Schema::table('subscriptions', function (Blueprint $table) {
    
            //$table->dropForeign(['rankings_ranking_groups_foreign']);

        });

        Schema::table('trade_item_post', function (Blueprint $table) {
    
            $table->dropForeign(['trade_item_type_id']);

        });

        Schema::table('trade_transaction', function (Blueprint $table) {
    
            
            $table->dropForeign(['trade_conversation_id']);
            // $table->dropForeign(['trade_id_prospect_item']);
            // $table->dropForeign(['trade_id_initiator_item']);
            // $table->dropForeign(['trade_id_initiator']);
            // $table->dropForeign(['trade_id_prospect']);
        });

        Schema::table('trade_transaction_history', function (Blueprint $table) {
    
            
            $table->dropForeign(['trade_trans_id']);
        });

        Schema::table('trxn_address_billing', function (Blueprint $table) {
    
            $table->dropForeign(['user_id']);
        });

        Schema::table('trxn_address_shipping', function (Blueprint $table) {
    
            $table->dropForeign(['user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
    
            // $table->dropForeign(['role_id']);
        });

        Schema::table('user_roles', function (Blueprint $table) {
    
            // $table->dropForeign(['role_id']);

            // $table->dropForeign(['user_id']);
        });

        Schema::table('videos', function (Blueprint $table) {
    
            $table->dropForeign(['trade_item_id']);

            $table->dropForeign(['user_id']);
        });

        Schema::table('trade_transaction_review', function (Blueprint $table) {
    
            $table->dropForeign(['trade_transaction_id']);
            $table->dropForeign(['prospect_id']);
            $table->dropForeign(['initiator_id']);
            $table->dropForeign(['item_id']);
        });

        Schema::table('trxn_shopping_cart', function (Blueprint $table) {

            $table->dropForeign(['user_id']);
        });

        


        Schema::dropIfExists('websockets_statistics_entries');

        /*
            TODO: will have to be done before adding e2e tests
             - wishlist table foreign keys and drop
             
        
        */
    }
}
