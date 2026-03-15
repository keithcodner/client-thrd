<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class FileCircle extends Model
{
    protected $table = 'files_circles';

    protected $fillable = [
        'reference_id',
        'table_reference_name',
        'file_store_an_id',
        'filename',
        'foldername',
        'status',
        'verify_status',
        'type',
        'file_order',
        'created_at',
        'updated_at',
    ];
}