<?php

namespace App\Models;

use App\Models\Like;
use App\Models\User;
use App\Models\Core\FileStored;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Posts extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $primaryKey  = 'id';

    protected $fillable = [ 
        'user_id',
        'comments_id',
        'likes_id', 
        'post_an_id',
        'file_store_an_id',
        'body',
        'status',
        'type', //general or pronetwork_group_profile
        'isVisible',
        'shareLink',
        'views',
    ];

    //check if user already liked the web comment/post
    public function likedBy(User $user)
    {
        return $this->likes->contains('user_id', $user->id);
    }

    public function ownedBy(User $user)
    {
        return $user->id === $this->user_id;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'post_id');
    }

    public function fileStored()
    {
        return $this->hasMany(FileStored::class, 'trade_item_post_id');
    }

}
