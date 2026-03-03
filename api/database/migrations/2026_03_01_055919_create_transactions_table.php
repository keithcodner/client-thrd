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
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('stripe_payment_intent_id', 5000)->nullable();
            $table->integer('credits_amount')->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('currency', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('paid_at')->timestamp()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
