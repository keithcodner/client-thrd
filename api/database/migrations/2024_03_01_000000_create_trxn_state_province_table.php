<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxnStateProvinceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trxn_state_province', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->integer('country_id')->default(0);
            $table->string('state', 111)->nullable()->default(null);
            $table->double('tax')->default(0);
            $table->integer('status')->default(1);
            $table->integer('owner_id')->default(0);

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
        Schema::dropIfExists('trxn_state_province');
    }
}
