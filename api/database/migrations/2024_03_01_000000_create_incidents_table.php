<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('incident_an_id', 255)->nullable()->default(null);
            $table->integer('incident_id')->nullable()->default(null);
            $table->string('incident_target_reason_table', 255)->nullable()->default(null);
            $table->string('incident_summary', 300)->nullable()->default(null);
            $table->string('incident_type', 255)->nullable()->default(null);
            $table->string('incident_status', 255)->nullable()->default(null);
            $table->text('incident_data1')->nullable()->default(null);
            $table->text('incident_data2')->nullable()->default(null);
            $table->text('incident_data3')->nullable()->default(null);
            $table->text('incident_data4')->nullable()->default(null);
            $table->text('incident_data5')->nullable()->default(null);
            $table->longText ('incident_description')->nullable()->default(null);
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
        Schema::dropIfExists('incidents');
    }
}
