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
        Schema::table('search', function (Blueprint $table) {
            // Add new tracking fields
            $table->json('filters')->nullable()->after('result_num');
            $table->text('user_agent')->nullable()->after('filters');
            $table->text('referrer')->nullable()->after('user_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('search', function (Blueprint $table) {
            $table->dropColumn(['filters', 'user_agent', 'referrer']);
        });
    }
};
