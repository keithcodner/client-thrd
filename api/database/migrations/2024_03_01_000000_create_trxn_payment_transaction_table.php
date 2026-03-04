<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxnPaymentTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trxn_payment_transaction', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('user_id')->nullable()->default(null);
            $table->integer('cart_id')->nullable()->default(null);
            $table->double('reward_point')->nullable()->default(0);
            $table->text('payload')->nullable()->default(0);
            $table->double('reward_dolar')->default(0);
            $table->text('txn_number')->nullable()->default(null);
            $table->double('amount')->nullable()->default(0);
            $table->string('currency_sign', 255)->nullable()->default(null);
            $table->string('currency_code', 255)->nullable()->default(null);
            $table->double('currency_value')->default(0);
            $table->string('method', 255)->nullable()->default(null);
            $table->string('txnid', 255)->nullable()->default(null);
            $table->text('details')->nullable()->default(null);
            $table->text('payload')->nullable()->default(null);
            $table->string('type', 255)->nullable()->comment('plus, minus')->default(null);
            $table->string('status', 255)->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);

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
        Schema::dropIfExists('trxn_payment_transaction');
    }
}
