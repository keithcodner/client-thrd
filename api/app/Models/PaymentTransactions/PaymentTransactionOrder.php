<?php

namespace App\Models\PaymentTransactions;

use App\Models\PaymentTransactions\PaymentTransaction;
use App\Models\PaymentTransactions\PaymentTransactionAddressBilling;
use App\Models\PaymentTransactions\PaymentTransactionAddressShipping;
use App\Models\PaymentTransactions\PaymentTransactionHistory;
use App\Models\PaymentTransactions\PaymentTransactionOrderHistory;
use App\Models\PaymentTransactions\PaymentTransactionOrderItem;
use App\Models\PaymentTransactions\PaymentTransactionShopper;
use App\Models\PaymentTransactions\PaymentTransactionShoppingCart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransactionOrder extends Model
{
    use HasFactory;

    protected $table = 'trxn_order';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
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

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function cart()
    {
        return $this->hasOne(PaymentTransactionShoppingCart::class, 'cart_id');
    }

    public function payment_transaction()
    {
        return $this->hasOne(PaymentTransaction::class,  'id', 'transaction_id');
    }

    public function payment_transaction_history()
    {
        return $this->hasMany(PaymentTransactionHistory::class, 'transaction_id', 'transaction_id');
    }

    public function order_history()
    {
        return $this->hasMany(PaymentTransactionOrderHistory::class, 'order_id', 'id');
    }

    public function order_items()
    {
        return $this->hasMany(PaymentTransactionOrderItem::class, 'order_id', 'id');
    }

    public function shopper()
    {
        return $this->hasOne(PaymentTransactionShopper::class, 'id', 'shopper_id');
    }

    public function shipping()
    {
        return $this->hasOne(PaymentTransactionAddressShipping::class, 'id', 'trxn_ship_id');
    }

    public function billing()
    {
        return $this->hasOne(PaymentTransactionAddressBilling::class, 'id', 'trxn_bill_id');
    }



   


}
