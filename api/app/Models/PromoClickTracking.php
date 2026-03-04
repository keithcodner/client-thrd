<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoClickTracking extends Model
{
    protected $table = 'promo_click_tracking';
    
    protected $fillable = [
        'promo_type',
        'action',
        'ip_address',
        'user_agent',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
