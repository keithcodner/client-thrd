<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings_site', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('name', 255)->nullable()->default(null);
            $table->text('value')->nullable()->default(null);
            $table->text('type1')->nullable()->default(null);
            $table->text('type2')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->text('op4')->nullable()->default(null);
            $table->text('op5')->nullable()->default(null);
            $table->text('op6')->nullable()->default(null);
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
        Schema::dropIfExists('settings_site');
    }
}
