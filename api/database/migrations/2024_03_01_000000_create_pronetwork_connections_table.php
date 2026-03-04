<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePronetworkConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pronetwork_connections', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('net_group_id')->nullable()->default(null);
            $table->bigInteger('net_request_id')->nullable()->default(null);
            $table->string('an_id', 255)->nullable()->default('');
            $table->bigInteger('initiator_user_id')->nullable()->default(null);
            $table->bigInteger('accepter_user_id')->nullable()->default(null);
            $table->string('isConnected', 50)->nullable()->default('');
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
        Schema::dropIfExists('pronetwork_connections');
    }
}
