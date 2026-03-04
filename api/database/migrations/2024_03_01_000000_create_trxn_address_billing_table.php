<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrxnAddressBillingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trxn_address_billing', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->string('addr_street', 100)->nullable()->default(null);
            $table->string('addr_zip', 50)->nullable()->default(null);
            $table->string('addr_postal_code', 50)->nullable()->default(null);
            $table->string('addr_country', 100)->nullable()->default(null);
            $table->string('addr_state', 100)->nullable()->default(null);
            $table->string('addr_province', 100)->nullable()->default(null);
            $table->string('addr_phone_number', 100)->nullable()->default(null);
            $table->string('addr_area_code', 100)->nullable()->default(null);
            $table->string('addr_city', 100)->nullable()->default(null);
            $table->string('addr_street_num', 100)->nullable()->default(null);
            $table->string('addr_apart_num', 100)->nullable()->default(null);
            $table->string('addr_po_box', 100)->nullable()->default(null);
            $table->string('addr_floor_num', 100)->nullable()->default(null);
            $table->string('addr_unit', 100)->nullable()->default(null);
            $table->string('addr_suite', 100)->nullable()->default(null);
            $table->string('addr_department', 100)->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);
            $table->dateTime('created_at')->nullable()->default(null);

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
        Schema::dropIfExists('trxn_address_billing');
    }
}
