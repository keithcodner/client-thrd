<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileTemporary extends Model
{
    use HasFactory;

    protected $table = 'files_temporary';
    protected $primaryKey  = 'id';

    protected $fillable = ['file_temp_an_id', 'filename', 'foldername'];
}
