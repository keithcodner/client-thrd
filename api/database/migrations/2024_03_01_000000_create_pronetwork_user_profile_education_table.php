<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePronetworkUserProfileEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pronetwork_user_profile_education', function (Blueprint $table) {
            $table->id(); // id (auto-increment primary key)
            $table->unsignedBigInteger('user_id'); // INT NOT NULL, assuming this will link to a users table
            $table->string('school', 255)->nullable(); // VARCHAR(255), nullable
            $table->string('degree', 255)->nullable(); // VARCHAR(255), nullable
            $table->string('field_of_study', 255)->nullable(); // VARCHAR(255), nullable
            $table->string('grade', 50)->nullable(); // VARCHAR(50), nullable
            $table->text('description')->nullable(); // TEXT, nullable
            $table->string('location_city', 255)->nullable(); // VARCHAR(255), nullable
            $table->string('location_country', 255)->nullable(); // VARCHAR(255), nullable
            $table->string('location_state_province', 255)->nullable(); // VARCHAR(255), nullable
            $table->string('location_state_province_abbrv', 255)->nullable(); // VARCHAR(255), nullable
            $table->date('start_date')->nullable(); // DATE, nullable
            $table->date('end_date')->nullable(); // DATE, nullable
            $table->string('status', 50)->nullable(); // VARCHAR(50), nullable
            $table->string('type', 50)->nullable(); // VARCHAR(50), nullable
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
        Schema::dropIfExists('pronetwork_user_profile_education');
    }
}
