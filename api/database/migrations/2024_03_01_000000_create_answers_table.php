<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->string('ua_ui_id', 15)->nullable()->default(null);
            $table->string('ua_description', 5000)->nullable()->default(null);
            $table->string('ua_likes', 15)->nullable()->default(null);
            $table->string('ua_dislikes', 15)->nullable()->default(null);
            $table->string('ua_shares', 15)->nullable()->default(null);
            $table->date('ua_date_created')->nullable()->default(null);

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
        Schema::dropIfExists('answers');
    }
}
