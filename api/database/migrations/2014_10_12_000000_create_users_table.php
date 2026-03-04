<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            //$table->bigInteger('role_id')->nullable()->default(null);
            $table->string('alpha_num_id', 100)->nullable()->default(null);
            $table->string('firstname', 50)->nullable()->default(null);
            $table->string('lastname', 50)->nullable()->default(null);
            $table->string('username', 50)->nullable()->default(null);
            $table->string('name', 255)->nullable()->default(null);
            $table->string('email', 255);
            $table->string('avatar', 255)->nullable()->default('users/avatar.png');
            $table->string('email_IsVerified', 50)->nullable()->default('no');
            $table->string('user_IsVerified', 50)->nullable()->default('no');
            $table->string('email_VerifiedToken', 255)->nullable()->default(null);
            $table->string('change_PasswordToken', 255)->nullable()->default(null);
            $table->string('password', 255);
            $table->string('password_try', 50)->nullable()->default(null);
            $table->string('status', 50)->nullable()->default('active');
            $table->string('remember_token', 100)->nullable()->default(null);
            $table->longText ('user_settings')->nullable()->default(null);
            $table->string('type', 50)->nullable()->default(null);
            $table->string('telephone', 50)->nullable()->default(null);
            $table->longText ('about')->nullable()->default(null);
            $table->longText ('contact')->nullable()->default(null);
            $table->string('links', 500)->nullable()->default(null);
            $table->longText ('history')->nullable()->default(null);
            $table->longText ('friend_list')->nullable()->default(null);
            $table->longText ('vid_fav')->nullable()->default(null);
            $table->longText ('trade_fav')->nullable()->default(null);
            $table->string('phone_num', 50)->nullable()->default(null);
            $table->string('isStoreOpen', 50)->nullable()->default(null);
            $table->string('identity', 50)->nullable()->default('anonymous');
            $table->string('intrests', 5000)->nullable()->default(null);
            $table->string('yourLocation', 5000)->nullable()->default(null);
            $table->longText('who_i_sub_to')->nullable()->default(null);
            $table->longText('who_sub_to_me')->nullable()->default(null);
            $table->string('who_i_sub_to_count', 15)->nullable()->default(0);
            $table->string('who_sub_to_me_count', 15)->nullable()->default(0);
            $table->string('registerIP', 50)->nullable()->default(null);
            $table->string('lastLoginIP', 50)->nullable()->default(null);
            $table->dateTime('suspend_reactive')->nullable()->default('1993-02-14 00:00:00');
            $table->dateTime('email_verified_at')->nullable()->default(null);
            $table->dateTime('birthdate')->nullable()->default(null);
            $table->dateTime('last_login')->nullable()->default(null);
            $table->string('user_lat', 200)->nullable()->default(null);
            $table->string('user_long', 200)->nullable()->default(null);
            $table->string('user_city', 200)->nullable()->default(null);
            $table->string('default_km_range', 25)->nullable()->default(null);
            $table->string('language', 25)->nullable()->default(null);
            $table->string('searchable', 25)->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);

            // Indexes
            //$table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        

        Schema::dropIfExists('users');
    }
};
