<?php

namespace App\Models\Local;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitiesCanada extends Model
{
    use HasFactory;

    protected $table = 'cities_canada';
    //protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
        'city',
        'city_ascii',
        'province_id',
        'province_name',
        'lat',
        'lng',
        'population',
        'density',
        'timezone',
        'ranking',
        'postal',
    ];
}
