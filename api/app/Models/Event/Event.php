<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\FileEvent;
use App\Models\PaymentTransactions\PaymentTransactionOrder;
//use App\Models\Transactions\;
use App\Models\Posts;
use App\Models\User;



class Event extends Model
{
    protected $table = 'event';

    protected $fillable = [
        'user_from_id',
        'user_to_id',
        '_id',
        'order_id',
        'post_id',
        'file_store_event_id',
        'event_group_id',
        'event_an_id',
        'name',
        'event_date_time',
        'event_date_time_start_range',
        'event_date_time_end_range',
        'description',
        'type',
        'type_second',
        'status',
        'status_second',
        'link',
        'isVisibleToOthers',
        'category',
        'color',
        'created_at',
        'updated_at',
    ];

    /*
        Event Types:
        - Self Event: type = 'self_event', type_second = 'todo' // basically when a user makes a note for themselves

    */

    public $timestamps = false; // Because your SQL defines timestamps manually with NULL default

    protected $dates = [
        'event_date_time',
        'event_date_time_start_range',
        'event_date_time_end_range',
        'created_at',
        'updated_at',
    ];

    // Example Relationships (optional, based on assumed structure)
    
    public function eventGroup()
    {
        return $this->belongsTo(EventGroup::class, 'event_group_id');
    }
    
    public function userFrom()
    {
        return $this->belongsTo(User::class, 'user_from_id');
    }

    public function userTo()
    {
        return $this->belongsTo(User::class, 'user_to_id');
    }

    public function order()
    {
        return $this->belongsTo(PaymentTransactionOrder::class, 'order_id');
    }

    public function post()
    {
        return $this->belongsTo(Posts::class, 'post_id');
    }

    public function fileStore()
    {
        return $this->belongsTo(FileEvent::class, 'file_store_event_id');
    }
}
