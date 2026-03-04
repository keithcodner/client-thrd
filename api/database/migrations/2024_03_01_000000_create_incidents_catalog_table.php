<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidents_catalog', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('incident_name', 1000)->nullable()->default(null);
            $table->double('incident_rate')->nullable()->default(null);
            $table->string('incident_priority', 300)->nullable()->default(null);
            $table->string('incident_status', 300)->nullable()->default(null);
            $table->integer('incident_threshold')->nullable()->default(null);
            $table->string('incident_op_1', 500)->nullable()->default(null);
            $table->string('incident_op_2', 500)->nullable()->default(null);
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
        Schema::dropIfExists('incidents_catalog');
    }
}
