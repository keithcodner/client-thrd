<?php

namespace App\Http\Controllers\E_Commerce\PaymentLogic;

use App\Http\{
    Controllers\Controller,
    Controllers\Core\NotificationsController,
};

use App\{
    Models\PaymentTransactions\PaymentTransaction,
    Models\PaymentTransactions\PaymentTransactionAddressBilling,
    Models\PaymentTransactions\PaymentTransactionAddressShipping,
    Models\PaymentTransactions\PaymentTransactionHistory,
    Models\PaymentTransactions\PaymentTransactionOrderHistory,
    Models\PaymentTransactions\PaymentTransactionOrder,
    Models\PaymentTransactions\PaymentTransactionOrderItem,
    Models\PaymentTransactions\PaymentTransactionPaymentProcessor,
    Models\PaymentTransactions\PaymentTransactionShopper,
    Models\PaymentTransactions\PaymentTransactionShoppingCart,
    Models\PaymentTransactions\PaymentTransactionTrackProductStat,
    Models\PaymentTransactions\PaymentTransactionInvoice,
    Models\PaymentTransactions\PaymentTransactionInvoiceItem,
    Models\PaymentTransactions\PaymentTransactionVAT,

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


class PaymentTransactionController extends Controller
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

    public function createPaymentTransaction(
        $cartObject, 
        $payload, 
        $user_id, 
        $order_id, 
        $cart_id, 
        $amountTotal, 
        $sessionId, 
        $shipping_amount,
        $tax_rate,
        $tax_amount,
        $total_amount,
        $subtotal_amount,
        $qty,
    ){
        try {
            // Create PaymentTransaction
            $paymentTransaction = PaymentTransaction::create([
                "user_id" =>  $user_id,
                'order_id' => $order_id,
                'cart_id' => $cart_id,
                "amount" => $amountTotal,
                "currency_sign" => '$',
                "currency_code" => 'USD',
                "method" => 'Stripe',
                "txnod" => '',
                "txn_number" => $sessionId,
                "payload" => $payload,
                "details" => 'Payment Completed',
                "type" => 'payment',
                "status" => 'completed',
            ]);
            \Log::info("PaymentTransaction created: " . $paymentTransaction->id);

            // Find the related PaymentTransactionOrder
            $trxnOrder = PaymentTransactionOrder::where('id', $order_id)->first();
            if ($trxnOrder) {
                // Update order status
                $trxnOrder->update([
                    "transaction_id" => $paymentTransaction->id,
                    'totalQty' => $qty,
                    'pay_amount' => $total_amount,
                    'tax' => $tax_amount,
                    'tax_location' => $tax_rate,
                    'packing_cost' => $shipping_amount,
                    'status' => 'completed',
                    'totalQty' => '1',
                    'payment_status' => 'paid',
                ]);
                \Log::info("PaymentTransactionOrder updated: " . $trxnOrder->id);

                // Copy order data to PaymentTransactionHistory with status paid
                $paymentTransactionHistory = PaymentTransactionHistory::create([
                    "user_id" =>  $user_id,
                    'order_id' => $order_id,
                    'cart_id' => $cart_id,
                    "transaction_id" => $paymentTransaction->id,
                    "amount" => $amountTotal,
                    "currency_sign" => '$',
                    "currency_code" => 'USD',
                    "method" => 'Stripe',
                    "txnod" => '',
                    "txn_number" => $sessionId,
                    "payload" => $payload,
                    "details" => 'Payment Completed',
                    "type" => 'record',
                    "status" => 'paid', // updated status
                ]);
                \Log::info("PaymentTransactionHistory created for paid status: " . $paymentTransactionHistory->id);

                // Create PaymentTransactionOrderHistory
                $paymentTransactionOrderHistory = PaymentTransactionOrderHistory::create([
                    'user_id' => $user_id,
                    'cart_id' => $trxnOrder->cart_id,
                    'order_id' => $trxnOrder->id,
                    'trxn_ship_id' => $trxnOrder->trxn_ship_id,
                    'trxn_bill_id' => $trxnOrder->trxn_bill_id,
                    'shopper_id' => $trxnOrder->shopper_id,
                    "transaction_id" => $paymentTransaction->id,
                    'totalQty' => $qty,
                    'pay_amount' => $total_amount,
                    'tax' => $tax_amount,
                    'tax_location' => $tax_rate,
                    'packing_cost' => $shipping_amount,
                    'status' => 'completed',
                    'payment_status' => 'paid',
                ]);
                \Log::info("PaymentTransactionOrderHistory created: " . $paymentTransactionOrderHistory->id);

                // Update all order items statuses
                PaymentTransactionOrderItem::where('order_id', $order_id)->update([
                    "transaction_id" => $paymentTransaction->id,
                    'tax' => $tax_rate,
                    'status' => 'completed',
                    'payment_status' => 'Completed',
                ]);
                \Log::info("PaymentTransactionOrderItems updated for order_id: " . $order_id);
            } else {
                \Log::warning("PaymentTransactionOrder not found for order_id: " . $order_id);
            }
        } catch (\Throwable $th) {
            \Log::error("Stripe Transaction and/or Order creation Error: " . $th->getMessage());
        }
    }
}
