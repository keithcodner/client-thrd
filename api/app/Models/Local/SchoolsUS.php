<?php

namespace App\Models\Local;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolsUS extends Model
{
    use HasFactory;

    protected $table = 'school_us_data';
    //protected $primaryKey  = 'id';

    // Specify fillable fields for mass assignment
    protected $fillable = [
        'Geo_Point',
        'Geo_Shape',
        'OBJECTID',
        'IPEDSID',
        'NAME',
        'ADDRESS',
        'CITY',
        'STATE',
        'ZIP',
        'ZIP4',
        'TELEPHONE',
        'TYPE',
        'STATUS',
        'POPULATION',
        'COUNTY',
        'COUNTYFIPS',
        'COUNTRY',
        'LATITUDE',
        'LONGITUDE',
        'NAICS_CODE',
        'NAICS_DESC',
        'SOURCE',
        'SOURCEDATE',
        'VAL_METHOD',
        'VAL_DATE',
        'WEBSITE',
        'STFIPS',
        'COFIPS',
        'SECTOR',
        'LEVEL_',
        'HI_OFFER',
        'DEG_GRANT',
        'LOCALE',
        'CLOSE_DATE',
        'MERGE_ID',
        'ALIAS',
        'SIZE_SET',
        'INST_SIZE',
        'PT_ENROLL',
        'FT_ENROLL',
        'TOT_ENROLL',
        'HOUSING',
        'DORM_CAP',
        'TOT_EMP',
        'SHELTER_ID'
    ];
}
