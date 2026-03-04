<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePronetworkUserProfileInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pronetwork_user_profile_interests', function (Blueprint $table) {
            $table->id(); // Primary key: id (auto-increment)
            $table->unsignedBigInteger('user_id'); // Foreign key: user_id, INT NOT NULL
            $table->unsignedBigInteger('group_id'); // Foreign key: group_id, INT NOT NULL
            $table->string('type', 50)->nullable(); // Type VARCHAR(50), nullable
            $table->string('status', 50)->nullable(); // Status VARCHAR(50), nullable
            $table->bigInteger('order')->nullable()->default(null);
            $table->dateTime('created_at')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pronetwork_user_profile_interests');
    }
}
