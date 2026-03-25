<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EventContent extends Model
{
    protected $table = 'event_content';

    protected $primaryKey = 'id';
    public $incrementing = false; // id is BIGINT(19) NOT NULL DEFAULT '0', not auto-increment
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'an_id',
        'order',
        'content1',
        'content2',
        'content3',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false; // Since created_at and updated_at are manually handled

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
