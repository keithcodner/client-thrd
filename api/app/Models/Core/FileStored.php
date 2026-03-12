<?php

namespace App\Models\Core;

use App\Models\Item;
use App\Models\Posts;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileStored extends Model
{
    use HasFactory;

    protected $table = 'files_stored';
    protected $primaryKey  = 'id';
    
    protected $fillable = ['circle_item_post_id', 'wishlist_item_id', 'feed_post_id', 'file_store_an_id', 'file_store_wishlist_an_id', 'filename', 'foldername', 'status', 'verify_status', 'type', 'order'];

    public function item()
    {
        return $this->belongsTo(Item::class, 'file_store_an_id');
    }

    public function post()
    {
        return $this->belongsTo(Posts::class, 'file_store_an_id');
    }

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class, 'file_store_an_id');
    }

    
}
