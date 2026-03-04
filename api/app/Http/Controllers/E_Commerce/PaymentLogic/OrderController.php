<?php

namespace App\Http\Controllers\E_Commerce\PaymentLogic;

use App\Http\{
    Controllers\Controller,
    Controllers\Core\NotificationsController,
};

use App\{
    Models\PaymentTransactions\PaymentTransaction,
    Models\PaymentTransactions\PaymentTransactionPaymentTranscation,
    Models\PaymentTransactions\PaymentTransactionAddressBilling,
    Models\PaymentTransactions\PaymentTransactionAddressShipping,
    Models\PaymentTransactions\PaymentTransactionPaymentTranscationHistory,
    Models\PaymentTransactions\PaymentTransactionHistory,
    Models\PaymentTransactions\PaymentTransactionOrderHistory,
    Models\PaymentTransactions\PaymentTransactionOrder,
    Models\PaymentTransactions\PaymentTransactionOrderItem,
    Models\PaymentTransactions\PaymentTransactionMembership,
    Models\PaymentTransactions\PaymentTransactionPaymentProcessor,
    Models\PaymentTransactions\PaymentTransactionShopper,
    Models\PaymentTransactions\PaymentTransactionShoppingCart,
    Models\PaymentTransactions\PaymentTransactionTrackProductStat,
    Models\PaymentTransactions\PaymentTransactionInvoice,
    Models\PaymentTransactions\PaymentTransactionInvoiceItem,
    Models\PaymentTransactions\PaymentTransactionVAT,
    Models\PaymentTransactions\PaymentTransactionProduct,

    Models\PaymentTransactions\PaymentTransactionCountry,
    Models\PaymentTransactions\PaymentTransactionCoupon,
    Models\PaymentTransactions\PaymentTransactionCurrency,
    Models\CitiesCanada,
    Models\Local\CitiesUS,

    Models\User
};

use Illuminate\{
    Support\Facades\Session,
    Support\Facades\Log,
    Support\Str,
    Http\Request
};

use Illuminate\Support\Facades\Redirect;
use Throwable;

class OrderController extends Controller
{
    public function __construct()
    {
        
    }


    public function index()
    {
        return view('old1.search.search');
    }

    public function tests(Request $request)
    {
        
    }

    public function createOrderTransaction($returnedCartTrxnData, $clientCartData, $returnedShopperTrxnData)
    {
        // 1. Create the order transaction
        $orderPaymentTransaction = PaymentTransactionOrder::create([
            'user_id' => auth()->user()->id,
            'cart_id' => $returnedCartTrxnData->id,
            'trxn_ship_id' => $returnedShopperTrxnData->trxn_shipping_id,
            'trxn_bill_id' => $returnedShopperTrxnData->trxn_billing_id,
            'shopper_id' => $returnedShopperTrxnData->id,
            'payment_status' => 'unpaid',
            'totalQty' => '1',
            'status' => 'pending',
        ]);

        $orderHistoryPaymentTransaction = PaymentTransactionOrderHistory::create([
            'order_id' => $orderPaymentTransaction->id,
            'user_id' => auth()->user()->id,
            'cart_id' => $returnedCartTrxnData->id,
            'trxn_ship_id' => $returnedShopperTrxnData->trxn_shipping_id,
            'trxn_bill_id' => $returnedShopperTrxnData->trxn_billing_id,
            'shopper_id' => $returnedShopperTrxnData->id,
            'payment_status' => 'unpaid',
            'totalQty' => '1',
            'status' => 'pending',
        ]);

        // 2. Loop through each cart item and create order item records
        $orderListOrder = 0;
        foreach ($clientCartData['cartProductList'] as $item) {
            PaymentTransactionOrderItem::create([
                'order_id' => $orderPaymentTransaction->id,
                'cart_id' => $returnedCartTrxnData->id,
                'user_id' => auth()->user()->id,
                'shopper_id' => $returnedShopperTrxnData->id,
                'trxn_ship_id' => $returnedShopperTrxnData->trxn_shipping_id,
                'trxn_bill_id' => $returnedShopperTrxnData->trxn_billing_id,
                'product_id' => $item['productId'] ?? null, // <-- fallback to null if missing
                'name' => $item['name'] ?? $item['lineItemName'] ?? 'Unknown',
                'unit_price' => $item['unitPrice'] ?? $item['price'] ?? 0,
                'subtotal' => $item['accumulatedPrice'] ?? $item['price'] ?? 0,
                'pay_amount' => $item['accumulatedPrice'] ?? $item['price'] ?? 0,
                'order_list_order' => $orderListOrder++,
                'status' => 'pending',
            ]);
        }

        return $orderPaymentTransaction;
    }
}
