<?php

namespace App\Models\Local;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitiesUS extends Model
{
    use HasFactory;

    protected $table = 'cities_us';
    //protected $primaryKey  = 'id';

    protected $fillable = [
        'city',
        'city_ascii',
        'state_id',
        'state_name',
        'county_fips',
        'county_name',
        'lat',
        'lng',
        'population',
        'density',
        'source',
        'military',
        'incorporated',
        'timezone',
        'ranking',
        'zips'
    ];
}
