<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxnProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trxn_product', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('sku', 255)->nullable()->default(null);
            //$table->enum('product_type', ['normal','affiliate'])->default('normal');
            $table->string('product_type', 191);
            $table->text('affiliate_link')->nullable()->default(null);
            $table->integer('user_id')->default(0);
            $table->integer('category_id');
            $table->integer('subcategory_id')->nullable()->default(null);
            $table->integer('childcategory_id')->nullable()->default(null);
            $table->text('attributes')->nullable()->default(null);
            $table->text('name');
            $table->text('slug')->nullable()->default(null);
            $table->string('photo', 191);
            $table->string('thumbnail', 191)->nullable()->default(null);
            $table->string('file', 191)->nullable()->default(null);
            $table->string('size', 191)->nullable()->default(null);
            $table->string('size_qty', 191)->nullable()->default(null);
            $table->string('size_price', 191)->nullable()->default(null);
            $table->text('color')->nullable()->default(null);
            $table->double('price');
            $table->double('previous_price')->nullable()->default(null);
            $table->text('details')->nullable()->default(null);
            $table->integer('stock')->nullable()->default(null);
            $table->text('color_all')->nullable()->default(null);
            $table->text('size_all')->nullable()->default(null);
            $table->integer('stock_check')->nullable()->default(1);
            $table->text('policy')->nullable()->default(null);
            $table->tinyInteger('status')->default(1);
            $table->integer('views')->default(0);
            $table->string('tags', 191)->nullable()->default(null);
            $table->text('features')->nullable()->default(null);
            $table->text('colors')->nullable()->default(null);
            $table->tinyInteger('product_condition')->default(0);
            $table->string('ship', 191)->nullable()->default(null);
            $table->tinyInteger('is_meta')->default(0);
            $table->text('meta_tag')->nullable()->default(null);
            $table->text('meta_description')->nullable()->default(null);
            $table->string('youtube', 191)->nullable()->default(null);
            $table->enum('type', ['Physical','Digital','License']);
            $table->text('license')->nullable()->default(null);
            $table->text('license_qty')->nullable()->default(null);
            $table->text('link')->nullable()->default(null);
            $table->string('platform', 255)->nullable()->default(null);
            $table->string('region', 255)->nullable()->default(null);
            $table->string('licence_type', 255)->nullable()->default(null);
            $table->string('measure', 191)->nullable()->default(null);
            $table->tinyInteger('featured')->default(0);
            $table->tinyInteger('best')->default(0);
            $table->tinyInteger('top')->default(0);
            $table->tinyInteger('hot')->default(0);
            $table->tinyInteger('latest')->default(0);
            $table->tinyInteger('big')->default(0);
            $table->tinyInteger('trending')->default(0);
            $table->tinyInteger('sale')->default(0);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
            $table->tinyInteger('is_discount')->default(0);
            $table->date('discount_date')->nullable()->default(null);
            $table->text('whole_sell_qty')->nullable()->default(null);
            $table->text('whole_sell_discount')->nullable()->default(null);
            $table->tinyInteger('is_catalog')->default(0);
            $table->integer('catalog_id')->default(0);
            $table->integer('order')->default(0);
            $table->integer('language_id')->nullable()->default(null);
            $table->tinyInteger('preordered')->default(0);
            $table->string('minimum_qty', 191)->nullable()->default(null);

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
        Schema::dropIfExists('trxn_product');
    }
}
