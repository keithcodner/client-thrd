<?php

namespace App\Models\Core;

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileUserStored extends Model
{
    use HasFactory;

    protected $table = 'files_user_stored';
    protected $primaryKey  = 'id';

    protected $fillable = ['file_store_an_id', 'filename', 'foldername', 'status', 'verify_status', 'type'];

    public function item()
    {
        return $this->belongsTo(Item::class, 'file_store_an_id');
    }
}
