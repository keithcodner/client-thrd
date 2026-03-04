<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxnCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trxn_coupon', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('code', 191);
            $table->tinyInteger('type');
            $table->double('price');
            $table->string('times', 191)->nullable()->default(null);
            $table->integer('used')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('coupon_type', 255)->nullable()->default(null);
            $table->integer('category')->nullable()->default(null);
            $table->integer('sub_category')->nullable()->default(null);
            $table->integer('child_category')->nullable()->default(null);

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
        Schema::dropIfExists('trxn_coupon');
    }
}
