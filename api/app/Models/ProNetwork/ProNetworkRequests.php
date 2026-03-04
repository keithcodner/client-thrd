<?php

namespace App\Models\ProNetwork;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProNetworkRequests extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_requests';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'initiator_user_id',
        'accepter_user_id',
        'type', //person, group
        'status', //pending, accepted, denied
        'isAccepted', //true
        'created_at',
        'updated_at',
    ];

    public function user_accept()
    {
        return $this->belongsTo(User::class, 'accepter_user_id');
    }

    public function user_initiate()
    {
        return $this->belongsTo(User::class, 'initiator_user_id');
    }


}
