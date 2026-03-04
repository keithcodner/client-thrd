<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxnShoppingCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trxn_shopping_cart', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('cart_id')->nullable()->default(null);
            $table->string('status', 50)->nullable()->default('active');
            $table->text('cart_data')->nullable()->default(null);
            $table->string('expire_threshold', 50)->nullable()->default('30');
            $table->dateTime('created_at')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);

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
        Schema::dropIfExists('trxn_shopping_cart');
    }
}
