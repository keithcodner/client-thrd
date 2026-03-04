<?php

namespace App\Models\ProNetwork;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProNetworkGroup extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_group';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'target_id',
        'status',
        'type',
        'created_at',
        'updated_at',
    ];


}
