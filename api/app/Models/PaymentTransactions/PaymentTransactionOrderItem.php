<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionOrderItem extends Model
{
    use HasFactory;

    protected $table = 'trxn_order_item';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'order_id',
        'order_list_order',
        'product_id',
        'unit_price',
        'subtotal',
        'name',

        'user_id',
        'cart_id',
        'transaction_id',
        'trxn_ship_id',
        'trxn_bill_id',
        'shopper_id',
        'method',
        'shipping',
        'pickup_location',
        'totalQty',
        'pay_amount',
        'charge_id',
        'order_number',
        'payment_status',
        'customer_email',
        'customer_name',
        'customer_country',
        'customer_phone',
        'customer_address',
        'customer_city',
        'customer_zip',
        'shipping_name',
        'shipping_country',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_zip',
        'order_note',
        'coupon_code',
        'coupon_discount',
        'status',
        'created_at',
        'updated_at',
        'affilate_user',
        'affilate_charge',
        'currency_sign',
        'currency_name',
        'currency_value',
        'shipping_cost',
        'packing_cost',
        'tax',
        'tax_location',
        'dp',
        'pay_id',
        'wallet_price',
        'shipping_title',
        'packing_title',
        'customer_state',
        'shipping_state',
        'discount',
        'affilate_users',
        'commission',
    ];


}
