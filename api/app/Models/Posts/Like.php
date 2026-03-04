<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'user_id',
        'post_id',
        'item_id',
        'comment_id',
        'lk_status', //active and in-active
        'lk_type', // either like, favourite, favourite_item
        'lk_value',
    ];

}
