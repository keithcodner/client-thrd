<?php

namespace App\Models\Circles;

use Illuminate\Database\Eloquent\Model;

class CircleDetail extends Model
{
    protected $table = 'circles_details';

    protected $fillable = [
        'circle_id',
        'circle_idea_board_id',
        'file_store_circle_an_id',
        'file_store_circle_bg_img_an_id',
        'description',
        'transparency_percent',
        'blur_depth_value',
        'style_code',
        'notification_code',
        'privacy_state',
        'type',
        'status',
        'created_at',
        'update_at',
    ];
}