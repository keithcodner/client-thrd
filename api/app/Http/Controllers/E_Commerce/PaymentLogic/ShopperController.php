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
    Models\PaymentTransactions\PaymentTransactionSettingProcessorStripe,
    Models\PaymentTransactions\PaymentTransactionOrder,
    Models\PaymentTransactions\PaymentTransactionMembership,
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

class ShopperController extends Controller
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

    public function createShopperTransaction($clientCartData, $returnedAddrBillTrxnData, $returnedAddrShipTrxnData)
    {

        $cartObj = (array)$clientCartData;
        end($clientCartData);
        $data = prev($clientCartData);
        //dd($cartObj['cart']);
        $shopperPaymentTransaction = PaymentTransactionShopper::create([
            "user_id" => auth()->user()->id,
            "trxn_billing_id" => $returnedAddrBillTrxnData->id,
            "trxn_shipping_id" => $returnedAddrShipTrxnData->id,
            "firstname" => $cartObj['data']['addressBilling']['nameOnCard'],
            //"lastname" => $data['data']['addressBilling']['lastName'],
            "email" => $cartObj['data']['addressBilling']['email']
        ]); 

        //dd($shopperPaymentTransaction);
        return $shopperPaymentTransaction;
    }
}
