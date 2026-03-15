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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->nullable();
            $table->unsignedBigInteger('to_id')->nullable();
            $table->unsignedBigInteger('circle_id')->nullable();
            $table->string('conv_an_id', 300)->nullable();
            $table->string('title', 2000)->nullable();
            $table->text('content');
            $table->string('deleted_by_user_id', 100)->nullable();
            $table->string('deleted_by_from_id', 100)->nullable();
            $table->string('deleted_by_group_ids', 100)->nullable();
            $table->string('status', 2000)->default('active');
            $table->string('status_second', 2000)->nullable();
            $table->string('type', 100)->default('couple');
            $table->string('type_second', 100)->default('couple');
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
        Schema::dropIfExists('conversations');
    }
};