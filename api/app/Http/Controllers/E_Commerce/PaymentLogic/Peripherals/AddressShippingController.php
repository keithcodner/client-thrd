<?php

namespace App\Http\Controllers\E_Commerce\PaymentLogic\Peripherals;

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


class AddressShippingController extends Controller
{
    public function __construct()
    {
        
    }


    public function index(User $user)
    {
       
    }

    

    public function tests(Request $request)
    {
        
    }

    public function createAddressShippingTransaction($cartObj)
    {
        $cartObj = (array)$cartObj;
        //dd($cartObj);
        //$cartObj = end($cartObj);
        $data = prev($cartObj);
        //dd($cartObj['data']['addressBilling']['address1']);
        $addressShippingPaymentTransaction = PaymentTransactionAddressShipping::create([
            "user_id" => auth()->user()->id,
            "addr_street" => $cartObj['data']['addressShipping']['address1'],
            "addr_zip" => $cartObj['data']['addressShipping']['zip'],
            "addr_postal_code" => $cartObj['data']['addressShipping']['zip'],
            "addr_country" => $cartObj['data']['addressShipping']['country'],
            "addr_province" => $cartObj['data']['addressShipping']['state'],
            "addr_state" => $cartObj['data']['addressShipping']['state'],
            "addr_city" => $cartObj['data']['addressBilling']['city'], // need city
            //"addr_unit" => $data['data']['addressShipping']['unit'],
        ]); 

        return $addressShippingPaymentTransaction;
    }
}
