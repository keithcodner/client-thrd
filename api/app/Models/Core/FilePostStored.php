<?php

namespace App\Models\Core;

use App\Models\Item;
use App\Models\Posts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FilePostStored extends Model
{
    use HasFactory;

    protected $table = 'files_post_stored';
    protected $primaryKey  = 'id';

    protected $fillable = ['post_id', 'file_store_an_id', 'filename', 'foldername', 'status', 'verify_status',  'type', 'order'];


}
