<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class FileEvent extends Model
{
    protected $table = 'files_event';

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

    public $timestamps = false; // Your SQL uses nullable DATETIME fields, timestamps not auto-managed

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Optional: Example relationship if 'reference_id' relates to another model
    // public function someReference()
    // {
    //     return $this->belongsTo(SomeModel::class, 'reference_id');
    // }
}
