<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionTrackProductStat extends Model
{
    use HasFactory;

    protected $table = 'trxn_tracker_product_stat';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
        'product_id',
        'product_views',
        'created_at',
        'updated_at',
    ];


}
