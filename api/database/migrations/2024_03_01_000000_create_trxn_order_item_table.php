<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxnOrderItemTable extends Migration
{
    public function up()
    {
        Schema::create('trxn_order_item', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('order_id')->nullable()->default(0);
            $table->integer('order_list_order')->nullable()->default(0);
            $table->integer('product_id')->nullable()->default(0);
            $table->double('unit_price')->nullable();
            $table->double('subtotal')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('column_6')->nullable(); // Rename this properly
            $table->unsignedInteger('cart_id')->nullable()->default(0);
            $table->unsignedInteger('transaction_id')->nullable();
            $table->unsignedInteger('trxn_ship_id')->nullable();
            $table->unsignedInteger('trxn_bill_id')->nullable();
            $table->unsignedInteger('shopper_id')->nullable();

            $table->string('method')->nullable();
            $table->string('name', 1000)->nullable();
            $table->string('shipping')->nullable();
            $table->string('pickup_location')->nullable();
            $table->string('totalQty')->nullable();
            $table->double('pay_amount')->nullable();
            $table->string('charge_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('payment_status')->default('Pending');

            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_country')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_zip')->nullable();

            $table->string('shipping_name')->nullable();
            $table->string('shipping_country')->nullable();
            $table->string('shipping_email')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_zip')->nullable();

            $table->text('order_note')->nullable();
            $table->string('coupon_code')->nullable();
            $table->string('coupon_discount')->nullable();

            $table->enum('status', ['pending', 'processing', 'completed', 'declined', 'on delivery'])->default('pending');

            $table->string('affilate_user')->nullable();
            $table->string('affilate_charge')->nullable();

            $table->string('currency_sign', 10)->nullable();
            $table->string('currency_name', 10)->nullable();
            $table->double('currency_value')->nullable();

            $table->double('shipping_cost')->nullable();
            $table->double('packing_cost')->default(0);
            $table->double('tax')->nullable();
            $table->string('tax_location')->nullable();

            $table->boolean('dp')->default(0);
            $table->text('pay_id')->nullable();
            $table->double('wallet_price')->default(0);

            $table->text('shipping_title')->nullable();
            $table->text('packing_title')->nullable();

            $table->string('customer_state')->nullable();
            $table->string('shipping_state')->nullable();
            $table->integer('discount')->nullable()->default(0);

            $table->text('affilate_users')->nullable();
            $table->double('commission')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trxn_order_item');
    }
}
