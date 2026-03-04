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
        Schema::create('company_info', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 500)->nullable();
            $table->text('company_description')->nullable();
            $table->text('company_website')->nullable();
            $table->text('company_social_1')->nullable();
            $table->text('company_social_2')->nullable();
            $table->text('company_social_3')->nullable();
            $table->integer('social_clicks_1')->nullable();
            $table->integer('social_clicks_2')->nullable();
            $table->integer('social_clicks_3')->nullable();
            $table->integer('search_click')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_info');
    }
};
