<?php

namespace App\Models\Local;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolsCanada extends Model
{
    use HasFactory;

    protected $table = 'school_ca_data';
    //protected $primaryKey  = 'id';

    // Define the fillable attributes
    protected $fillable = [
        'Id',
        'Source_ID',
        'Facility_Name',
        'Facility_Type',
        'Authority_Name',
        'ISCED010',
        'ISCED020',
        'ISCED1',
        'ISCED2',
        'ISCED3',
        'ISCED4Plus',
        'OLMS_Status',
        'Full_Addr',
        'Unit',
        'Street_No',
        'Street_Name',
        'City',
        'Prov_Terr',
        'Postal_Code',
        'PRUID',
        'CSDNAME',
        'CSDUID',
        'Longitude',
        'Latitude',
        'Geo_Source',
        'Provider',
        'CMANAME',
        'CMAUID'
    ];

    // Optionally, you can define casts for your attributes
    protected $casts = [
        'Longitude' => 'float',
        'Latitude' => 'float',
    ];
}
