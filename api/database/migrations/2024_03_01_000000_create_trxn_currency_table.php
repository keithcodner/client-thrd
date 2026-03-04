<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxnCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trxn_currency', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('name', 30);
            $table->string('sign', 10);
            $table->double('value');
            $table->tinyInteger('is_default')->default(0);

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
        Schema::dropIfExists('trxn_currency');
    }
}
