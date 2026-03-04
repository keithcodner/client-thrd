<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaSchoolDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_ca_data', function (Blueprint $table) {
            $table->string('Id', 512); // VARCHAR(512)
            $table->string('Source_ID', 512); // VARCHAR(512)
            $table->string('Facility_Name', 512); // VARCHAR(512)
            $table->string('Facility_Type', 512); // VARCHAR(512)
            $table->string('Authority_Name', 512); // VARCHAR(512)
            $table->string('ISCED010', 512); // VARCHAR(512)
            $table->string('ISCED020', 512); // VARCHAR(512)
            $table->string('ISCED1', 512); // VARCHAR(512)
            $table->string('ISCED2', 512); // VARCHAR(512)
            $table->string('ISCED3', 512); // VARCHAR(512)
            $table->string('ISCED4Plus', 512); // VARCHAR(512)
            $table->string('OLMS_Status', 512); // VARCHAR(512)
            $table->string('Full_Addr', 512); // VARCHAR(512)
            $table->string('Unit', 512)->nullable(); // VARCHAR(512)
            $table->string('Street_No', 512)->nullable(); // VARCHAR(512)
            $table->string('Street_Name', 512); // VARCHAR(512)
            $table->string('City', 512); // VARCHAR(512)
            $table->string('Prov_Terr', 512); // VARCHAR(512)
            $table->string('Postal_Code', 512); // VARCHAR(512)
            $table->string('PRUID', 512); // VARCHAR(512)
            $table->string('CSDNAME', 512); // VARCHAR(512)
            $table->string('CSDUID', 512); // VARCHAR(512)
            $table->string('Longitude', 512); // VARCHAR(512)
            $table->string('Latitude', 512); // VARCHAR(512)
            $table->string('Geo_Source', 512); // VARCHAR(512)
            $table->string('Provider', 512); // VARCHAR(512)
            $table->string('CMANAME', 512); // VARCHAR(512)
            $table->string('CMAUID', 512); // VARCHAR(512)

            // You can also add any necessary indexes or constraints here
            // For example, if you want to set 'Id' as the primary key:
            $table->primary('Id'); // Uncomment if 'Id' should be the primary key

            // Timestamps (if needed)
            // $table->timestamps(); // Uncomment if you want to include created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_ca_data');
    }
}
