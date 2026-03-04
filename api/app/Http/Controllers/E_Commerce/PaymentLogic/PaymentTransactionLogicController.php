<?php

namespace App\Http\Controllers\E_Commerce\PaymentLogic;

use App\Http\{
    Controllers\Controller,
    Controllers\Core\NotificationsController,
    Controllers\E_Commerce\PaymentProviders\StripeController,
    Controllers\E_Commerce\PaymentLogic\OrderController,
    Controllers\E_Commerce\PaymentLogic\Peripherals\AddressBillingController,
    Controllers\E_Commerce\PaymentLogic\Peripherals\AddressShippingController,
    Controllers\E_Commerce\PaymentLogic\ShopperController,
    Controllers\E_Commerce\CartController
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

class PaymentTransactionLogicController extends Controller
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

    public function createPaymentTransactionByTransactionType($clientCartData, $trxnType)
    {
        $returnedCartTrxnData = '';
        $returnedOrderTrxnData = '';
        $returnedAddrShipTrxnData = '';
        $returnedAddrBillTrxnData = '';
        $returnedShopperTrxnData = '';

        $paymentTrxnTypeReturn = ''; // what is ultimately returned
        if($trxnType == 'stripe'){

            //dd($clientCartData);

            $returnedCartTrxnData = app(CartController::class)->createShoppingCartTransaction($clientCartData); //done
            

            $returnedAddrBillTrxnData = app(AddressBillingController::class)->createAddressBillingTransaction($clientCartData); //done
            $returnedAddrShipTrxnData = app(AddressShippingController::class)->createAddressShippingTransaction($clientCartData); //done
            $returnedShopperTrxnData = app(ShopperController::class)->createShopperTransaction($clientCartData, $returnedAddrBillTrxnData, $returnedAddrShipTrxnData); //done
            
            $returnedOrderTrxnData = app(OrderController::class)->createOrderTransaction($returnedCartTrxnData, $clientCartData, $returnedShopperTrxnData);
            $paymentTrxnTypeReturn = app(StripeController::class)->createStripePaymentTransaction($clientCartData, $returnedCartTrxnData, $returnedOrderTrxnData, $clientCartData);

            return $paymentTrxnTypeReturn;

        }else if($trxnType == 'stripe2'){

            $returnedShopperTrxnData = app(ShopperController::class)->createShopperTransaction($clientCartData, $returnedAddrBillTrxnData, $returnedAddrShipTrxnData); //done
            
            $returnedOrderTrxnData = app(OrderController::class)->createOrderTransaction($returnedCartTrxnData, $clientCartData, $returnedShopperTrxnData);
            $paymentTrxnTypeReturn = app(StripeController::class)->createStripePaymentTransaction($clientCartData, $returnedCartTrxnData, $returnedOrderTrxnData, $clientCartData);

            return $paymentTrxnTypeReturn;

        }else if($trxnType == 'applepay'){

        }else if($trxnType == 'squarepay'){

        }else if($trxnType == 'moneris'){

        }else if($trxnType == 'paypal'){

        }else if($trxnType == 'baintree'){

        }else{
            return 'Error: There was a payment error';
        }
    }
}
