<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_approvals', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('content_reason_id')->nullable()->default(null);
            $table->string('content_reason_table', 255)->nullable()->default(null);
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->string('approval_an_id', 255)->nullable()->default(null);
            $table->string('approval_human_id', 255)->nullable()->default(null);
            $table->string('approval_type', 255)->nullable()->default(null);
            $table->string('approval_summary', 255)->nullable()->default(null);
            $table->string('approval_desc', 255)->nullable()->default(null);
            $table->string('approval_status', 255)->nullable()->default(null);
            $table->string('approval_second_status', 255)->nullable()->default(null);
            $table->string('approval_third_status', 255)->nullable()->default(null);
            $table->string('approval_op_1', 3000)->nullable()->default(null);
            $table->string('approval_op_2', 3000)->nullable()->default(null);
            $table->string('approval_op_3', 3000)->nullable()->default(null);
            $table->string('approval_op_4', 3000)->nullable()->default(null);
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
        Schema::dropIfExists('content_approvals');
    }
}
