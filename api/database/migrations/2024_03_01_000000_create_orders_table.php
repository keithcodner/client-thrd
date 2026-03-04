<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('order_details_id')->nullable()->default(null);
            $table->string('orderNumber', 50);
            $table->date('orderDate');
            $table->date('requiredDate');
            $table->date('shippedDate')->nullable()->default(null);
            $table->string('status', 15);
            $table->text('comments')->nullable()->default(null);
            $table->integer('customerNumber');

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
        Schema::dropIfExists('orders');
    }
}
