<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolUsDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_us_data', function (Blueprint $table) {
            $table->id(); // If you don't want an auto-incrementing ID, you can remove this line
            $table->string('Geo_Point', 255)->nullable();
            $table->string('Geo_Shape', 255)->nullable();
            $table->string('OBJECTID', 255)->nullable();
            $table->string('IPEDSID', 255)->nullable();
            $table->string('NAME', 255)->nullable();
            $table->string('ADDRESS', 255)->nullable();
            $table->string('CITY', 255)->nullable();
            $table->string('STATE', 255)->nullable();
            $table->string('ZIP', 255)->nullable();
            $table->string('ZIP4', 255)->nullable();
            $table->string('TELEPHONE', 255)->nullable();
            $table->string('TYPE', 255)->nullable();
            $table->string('STATUS', 255)->nullable();
            $table->string('POPULATION', 255)->nullable();
            $table->string('COUNTY', 255)->nullable();
            $table->string('COUNTYFIPS', 255)->nullable();
            $table->string('COUNTRY', 255)->nullable();
            $table->string('LATITUDE', 255)->nullable();
            $table->string('LONGITUDE', 255)->nullable();
            $table->string('NAICS_CODE', 255)->nullable();
            $table->string('NAICS_DESC', 255)->nullable();
            $table->string('SOURCE', 255)->nullable();
            $table->string('SOURCEDATE', 255)->nullable();
            $table->string('VAL_METHOD', 255)->nullable();
            $table->string('VAL_DATE', 255)->nullable();
            $table->string('WEBSITE', 255)->nullable();
            $table->string('STFIPS', 255)->nullable();
            $table->string('COFIPS', 255)->nullable();
            $table->string('SECTOR', 255)->nullable();
            $table->string('LEVEL_', 255)->nullable();
            $table->string('HI_OFFER', 255)->nullable();
            $table->string('DEG_GRANT', 255)->nullable();
            $table->string('LOCALE', 255)->nullable();
            $table->string('CLOSE_DATE', 255)->nullable();
            $table->string('MERGE_ID', 255)->nullable();
            $table->string('ALIAS', 1000)->nullable();
            $table->string('SIZE_SET', 255)->nullable();
            $table->string('INST_SIZE', 255)->nullable();
            $table->string('PT_ENROLL', 255)->nullable();
            $table->string('FT_ENROLL', 255)->nullable();
            $table->string('TOT_ENROLL', 255)->nullable();
            $table->string('HOUSING', 255)->nullable();
            $table->string('DORM_CAP', 255)->nullable();
            $table->string('TOT_EMP', 255)->nullable();
            $table->string('SHELTER_ID', 255)->nullable();
            
            $table->collation = 'utf8mb3_unicode_ci'; // Set collation
            $table->engine = 'InnoDB'; // Set engine type
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_us_data');
    }
}