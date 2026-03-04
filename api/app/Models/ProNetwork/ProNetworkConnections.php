<?php

namespace App\Models\ProNetwork;

use App\Models\ProNetwork\ProNetworkUserProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProNetworkConnections extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_connections';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'net_group_id',
        'net_request_id',
        'an_id',
        'initiator_user_id',
        'accepter_user_id',
        'type', //connection or group_connection
        'isConnected',
        'status', 
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'initiator_user_id');
    }

    //Good example of a relation...only one is needed...smh (ProNetworkUserProfile is not required to have a relation to this model)
    public function pronetworkuserprofile_ref_init()
    {
        return $this->belongsTo(User::class, 'initiator_user_id');
    }

    public function pronetworkuserprofile_ref_accept()
    {
        return $this->belongsTo(User::class, 'accepter_user_id');
    }
    

    /*
        - more info about the above, first param is the referenced model, 
        - second param is prime id
        - third param is the secondary id from the ProNetworkUserProfile model
        - looking at some of the doc in 'belongsTo' method, it guesses the third param which was confusing me before. I didnt know it did that, but since these id's it can't guess, then i have to define it manually which i knew before...again...smh
    */


}
