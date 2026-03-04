<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesUsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities_us', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('city', 44)->nullable()->default(null);
            $table->string('city_ascii', 44)->nullable()->default(null);
            $table->string('state_id', 2)->nullable()->default(null);
            $table->string('state_name', 20)->nullable()->default(null);
            $table->integer('county_fips')->nullable()->default(null);
            $table->string('county_name', 30)->nullable()->default(null);
            $table->decimal('lat', 7, 4)->nullable()->default(null);
            $table->decimal('lng', 9, 4)->nullable()->default(null);
            $table->integer('population')->nullable()->default(null);
            $table->decimal('density', 7, 1)->nullable()->default(null);
            $table->string('source', 5)->nullable()->default(null);
            $table->string('military', 5)->nullable()->default(null);
            $table->string('incorporated', 5)->nullable()->default(null);
            $table->string('timezone', 30)->nullable()->default(null);
            $table->integer('ranking')->nullable()->default(null);
            $table->string('zips', 1847)->nullable()->default(null);

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
        Schema::dropIfExists('cities_us');
    }
}
