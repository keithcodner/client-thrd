<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxnOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trxn_order', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('user_id')->nullable()->default(null);
            $table->text('cart');
            $table->string('method', 255)->nullable()->default(null);
            $table->string('shipping', 255)->nullable()->default(null);
            $table->string('pickup_location', 255)->nullable()->default(null);
            $table->string('totalQty', 191);
            $table->double('pay_amount');
            $table->string('txnid', 255)->nullable()->default(null);
            $table->string('charge_id', 255)->nullable()->default(null);
            $table->string('order_number', 255);
            $table->string('payment_status', 255)->default('Pending');
            $table->string('customer_email', 255);
            $table->string('customer_name', 255);
            $table->string('customer_country', 191);
            $table->string('customer_phone', 255);
            $table->string('customer_address', 255)->nullable()->default(null);
            $table->string('customer_city', 255)->nullable()->default(null);
            $table->string('customer_zip', 255)->nullable()->default(null);
            $table->string('shipping_name', 255)->nullable()->default(null);
            $table->string('shipping_country', 191)->nullable()->default(null);
            $table->string('shipping_email', 255)->nullable()->default(null);
            $table->string('shipping_phone', 255)->nullable()->default(null);
            $table->string('shipping_address', 255)->nullable()->default(null);
            $table->string('shipping_city', 255)->nullable()->default(null);
            $table->string('shipping_zip', 255)->nullable()->default(null);
            $table->text('order_note')->nullable()->default(null);
            $table->string('coupon_code', 191)->nullable()->default(null);
            $table->string('coupon_discount', 191)->nullable()->default(null);
            //$table->enum('status', ['pending','processing','completed','declined','on delivery'])->default(pending);
            $table->string('status', 191)->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
            $table->string('affilate_user', 191)->nullable()->default(null);
            $table->string('affilate_charge', 191)->nullable()->default(null);
            $table->string('currency_sign', 10);
            $table->string('currency_name', 10);
            $table->double('currency_value');
            $table->double('shipping_cost');
            $table->double('packing_cost')->default(0);
            $table->double('tax');
            $table->string('tax_location', 191)->nullable()->default(null);
            $table->tinyInteger('dp')->default(0);
            $table->text('pay_id')->nullable()->default(null);
            $table->double('wallet_price')->default(0);
            $table->text('shipping_title')->nullable()->default(null);
            $table->text('packing_title')->nullable()->default(null);
            $table->string('customer_state', 191)->nullable()->default(null);
            $table->string('shipping_state', 191)->nullable()->default(null);
            $table->integer('discount')->default(0);
            $table->text('affilate_users')->nullable()->default(null);
            $table->double('commission')->default(0);

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
        Schema::dropIfExists('trxn_order');
    }
}
