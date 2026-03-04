<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->default(0)->nullable();
            $table->string('file_product_id', 15);
            $table->string('productCode', 15);
            $table->string('productName', 70);
            $table->string('productLine', 50);
            $table->string('productScale', 10);
            $table->string('productVendor', 50);
            $table->text('productDescription');
            $table->smallInteger('quantityInStock');
            $table->decimal('buyPrice', 10, 2);
            $table->decimal('MSRP', 10, 2);
            $table->string('href', 50)->default('');
            $table->string('tags', 50)->default('');
            $table->string('type', 50)->default('');
            $table->string('type_second', 50)->default('');
            $table->string('status', 50)->default('');
            $table->text('features')->default('');
            $table->string('mostPopular', 50)->default('');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            // Index for foreign key
            $table->index('user_id', 'fk_user_to_products');

            // Foreign key constraint
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onUpdate('no action')
                  ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}
