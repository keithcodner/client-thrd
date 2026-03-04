<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionAddressShipping extends Model
{
    use HasFactory;

    protected $table = 'trxn_address_shipping';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'user_id',
        'addr_street',
        'addr_zip',
        'addr_postal_code',
        'addr_country',
        'addr_state',
        'addr_province',
        'addr_phone_number',
        'addr_area_code',
        'addr_city',
        'addr_street_num',
        'addr_apart_num',
        'addr_po_box',
        'addr_floor_num',
        'addr_unit',
        'addr_suite',
        'addr_department',
    ];


}
