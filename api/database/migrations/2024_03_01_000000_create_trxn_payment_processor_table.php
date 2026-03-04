<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxnPaymentProcessorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trxn_payment_processor', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('subtitle', 191)->nullable()->default(null);
            $table->string('title', 191)->nullable()->default(null);
            $table->text('details')->nullable()->default(null);
            $table->string('name', 100)->nullable()->default(null);
            $table->enum('type', ['manual','automatic'])->nullable()->default('manual');
            $table->longText ('information')->nullable()->default(null);
            $table->string('keyword', 191)->nullable()->default(null);
            $table->string('currency_id', 191)->default(0);
            $table->integer('checkout')->default(1);
            $table->integer('deposit')->default(1);
            $table->integer('subscription')->default(1);

            // Indexes
            //$table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trxn_payment_processor');
    }
}
