<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('web_activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // For tracking non-logged visitors
            $table->uuid('visitor_uuid');

            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();

            $table->string('http_method', 10);
            $table->text('url');
            $table->string('route_name', 150)->nullable();
            $table->text('query_string')->nullable();

            $table->text('referrer')->nullable();
            $table->string('country', 100)->nullable();
            
            // Geographic Data
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('timezone', 50)->nullable();
            $table->string('isp', 255)->nullable();
            
            $table->string('device_type', 50)->nullable();
            
            // Performance Metrics
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->enum('request_type', ['page', 'api', 'asset', 'other'])->default('page')->nullable();
            
            // Engagement Metrics
            $table->unsignedInteger('time_spent_seconds')->nullable();
            $table->unsignedTinyInteger('scroll_depth_percent')->nullable();
            $table->boolean('is_exit')->default(false);
            $table->json('interactions')->nullable();
            
            // User Journey
            $table->boolean('is_landing_page')->default(false);
            $table->text('previous_page')->nullable();
            $table->string('conversion_type', 50)->nullable();
            $table->string('funnel_stage', 50)->nullable();
            
            // Marketing & Tracking
            $table->string('utm_source', 255)->nullable();
            $table->string('utm_medium', 255)->nullable();
            $table->string('utm_campaign', 255)->nullable();
            $table->string('utm_term', 255)->nullable();
            $table->string('utm_content', 255)->nullable();
            
            // Technical Details
            $table->unsignedSmallInteger('screen_width')->nullable();
            $table->unsignedSmallInteger('screen_height')->nullable();
            $table->string('browser_language', 10)->nullable();
            $table->string('connection_type', 20)->nullable();
            $table->boolean('is_bot')->default(false);
            
            // Banning
            $table->boolean('is_banned')->default(false);
            $table->string('session_id')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['visitor_uuid']);
            $table->index(['ip_address']);
            $table->index(['route_name']);
            $table->index(['session_id']);
            $table->index(['created_at']);
            $table->index(['ip_address', 'is_banned']);
            $table->index(['status_code']);
            $table->index(['is_landing_page']);
            $table->index(['conversion_type']);
            $table->index(['utm_source']);
            $table->index(['is_bot']);
            $table->index(['request_type']);
            $table->index(['country', 'city']);
            $table->index(['created_at', 'status_code']);
            $table->index(['user_id', 'session_id', 'created_at']);
            $table->index(['ip_address', 'created_at', 'is_bot']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('web_activity_logs');
    }
};
