<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesCanadaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities_canada', function (Blueprint $table) {
            $table->integer('id')->nullable()->default(null);
            $table->text('city')->nullable()->default(null);
            $table->text('city_ascii')->nullable()->default(null);
            $table->string('province_id')->nullable()->default(null);
            $table->string('province_name')->nullable()->default(null);
            $table->string('lat')->nullable()->default(null);
            $table->string('lng')->nullable()->default(null);
            $table->string('population')->nullable()->default(null);
            $table->string('density')->nullable()->default(null);
            $table->text('timezone')->nullable()->default(null);
            $table->integer('ranking')->nullable()->default(null);
            $table->text('postal')->nullable()->default(null);

            // Indexes

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities_canada');
    }
}
