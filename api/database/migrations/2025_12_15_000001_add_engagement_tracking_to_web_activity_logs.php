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
        Schema::table('web_activity_logs', function (Blueprint $table) {
            // Add event type for tracking views, clicks, etc.
            $table->enum('event_type', ['view', 'click', 'apply', 'share', 'save', 'other'])->nullable()->after('route_name');
            
            // Add related entity tracking (polymorphic relationship)
            $table->unsignedBigInteger('related_id')->nullable()->after('event_type');
            $table->string('related_type', 50)->nullable()->after('related_id'); // e.g., 'job', 'profile', 'post'
            
            // Add indexes for better performance
            $table->index(['event_type']);
            $table->index(['related_id', 'related_type']);
            $table->index(['event_type', 'related_id', 'related_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_activity_logs', function (Blueprint $table) {
            $table->dropIndex(['event_type', 'related_id', 'related_type']);
            $table->dropIndex(['related_id', 'related_type']);
            $table->dropIndex(['event_type']);
            
            $table->dropColumn(['event_type', 'related_id', 'related_type']);
        });
    }
};
