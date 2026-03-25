<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EventGroup extends Model
{
    protected $table = 'event_groups';

    protected $fillable = [
        'event_id',
        'user_id',
        'group_name',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false; // Since created_at and updated_at are manually handled

    // Optional: If you want to automatically cast created_at/updated_at as Carbon instances
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Relationships
    public function events()
    {
        return $this->hasMany(Event::class, 'event_group_id');
    }

    public function primaryEvent()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
