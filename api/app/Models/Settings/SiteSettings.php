<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    use HasFactory;

    protected $table = 'settings_site';
    protected $primaryKey  = 'id';

    protected $fillable = ['name', 'value', 'type1', 'type2', 'description', 'op4', 'op5', 'op6'];
}
