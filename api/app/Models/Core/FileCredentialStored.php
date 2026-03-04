<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileCredentialStored extends Model
{
    use HasFactory;

    protected $table = 'files_credentials_stored';
    protected $primaryKey  = 'id';

    protected $fillable = ['user_id', 'file_store_an_id', 'filename', 'foldername', 'status', 'verify_status', 'type', 'order'];
}
