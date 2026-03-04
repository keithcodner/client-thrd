<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePronetworkRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        Schema::create('pronetwork_requests', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('initiator_user_id')->nullable()->default(null);
            $table->bigInteger('accepter_user_id')->nullable()->default(null);
            $table->string('isAccepted', 50)->nullable()->default('false');
            $table->string('status', 100)->nullable()->default('');
            $table->string('type', 100)->nullable()->default('');
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
        Schema::dropIfExists('pronetwork_requests');
    }
}
