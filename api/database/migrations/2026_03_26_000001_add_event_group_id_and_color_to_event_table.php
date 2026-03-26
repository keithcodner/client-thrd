<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('event', function (Blueprint $table) {
            if (!Schema::hasColumn('event', 'event_group_id')) {
                $table->unsignedBigInteger('event_group_id')->nullable()->after('file_store_event_id');
            }
            if (!Schema::hasColumn('event', 'color')) {
                $table->string('color', 20)->nullable()->default('#ADC178')->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dropColumn(['event_group_id', 'color']);
        });
    }
};
