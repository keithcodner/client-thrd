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
    Models\PaymentTransactions\PaymentTransactionProduct,

    Models\PaymentTransactions\PaymentTransactionCountry,
    Models\PaymentTransactions\PaymentTransactionCoupon,
    Models\PaymentTransactions\PaymentTransactionCurrency,
    Models\CitiesCanada,
    Models\Local\CitiesUS,

    Models\User
};
use App\Http\Controllers\E_Commerce\CartController;
use Illuminate\{
    Support\Facades\Session,
    Support\Facades\Log,
    Support\Str,
    Http\Request
};

use Illuminate\Support\Facades\Redirect;
use Throwable;


class MembershipController extends Controller
{
    public function __construct()
    {
        
    }


    /*
                    *** how to use sessions ***
        - https://www.youtube.com/watch?v=Zap-LNZpvGA&ab_channel=CodeWithDary
        - put methond writes
        - push method adds
        - has method checks if session has key
        - all method gets all data in session
    */
    public function index(User $user)
    {
        $test = app(CartController::class)->clearCartSession();
        $products = PaymentTransactionProduct::where('status', 'active')->where('type', 'membership')->limit(3)->get();
        

        return view('old1.e-commerce.shopping.membership', [
            'products' => $products,
            'test' => $test,
        ]);
    }

    

    public function tests(Request $request)
    {
        
    }

    public function createMembershipTransaction(Request $request){

    }
}
