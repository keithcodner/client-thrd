<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePronetworkUserProfileAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pronetwork', function (Blueprint $table) {
            $table->id(); // Primary key: id (auto-increment)
            $table->unsignedBigInteger('user_id'); // Foreign key: user_id, INT NOT NULL

            $table->integer('profile_views_count')->default(0); // Profile views count
            $table->integer('interactive_count')->default(0); // Interactive count
            $table->integer('connections_count')->default(0); // Connections count

            $table->string('name')->nullable(); // Optional name field
            $table->string('value')->nullable(); // Optional value field

            // Optional operational fields
            $table->string('op_1')->nullable();
            $table->string('op_2')->nullable();
            $table->string('op_3')->nullable();
            $table->string('op_4')->nullable();
            $table->string('op_5')->nullable();

            $table->string('type', 50)->nullable(); // Type
            $table->string('status', 50)->nullable(); // Status

            $table->dateTime('created_at')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);

            // Foreign key constraint (optional but recommended)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pronetwork');
    }
}
