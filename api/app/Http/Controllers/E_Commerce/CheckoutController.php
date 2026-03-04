<?php

namespace App\Http\Controllers\E_Commerce;

use App\Http\{
    Controllers\Controller,
    Controllers\Core\NotificationsController,
    Controllers\E_Commerce\PaymentProviders\StripeController,
    Controllers\E_Commerce\PaymentLogic\OrderController,
    Controllers\E_Commerce\PaymentLogic\Peripherals\AddressBillingController,
    Controllers\E_Commerce\PaymentLogic\Peripherals\AddressShippingController,
    Controllers\E_Commerce\PaymentLogic\ShopperController,
    Controllers\E_Commerce\PaymentLogic\PaymentTransactionLogicController,
    Controllers\E_Commerce\CartController
    
};

use App\{
    Models\PaymentTransactions\PaymentTransaction,

    Models\PaymentTransactions\PaymentTransactionCountry,
    Models\PaymentTransactions\PaymentTransactionCoupon,
    Models\PaymentTransactions\PaymentTransactionCurrency,
    Models\Local\CitiesCanada,
    Models\Local\CitiesUS,
    Models\Shopping\Products,

    Models\User
};

use Illuminate\Support\Facades\Redirect;
use Illuminate\{
    Support\Facades\Session,
    Support\Str,
    Http\Request
};

use \Stripe\{
    Stripe
};


use Inertia\Inertia;
use Throwable;

/*
                ---How to use Stripe API and Keys---
    - https://www.youtube.com/watch?v=1KxD8J8CAFg&ab_channel=DaveHollingworth
*/

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'cors']);
    }

    public function testCheckoutSuccess(Request $request){
        return view('old1.e-commerce.checkout.successPage');
    }

    public function checkoutSuccessIndex(Request $request){
        //clear cart when this page loads
        app(CartController::class)->clearCartSession();
        return Inertia::render('ECommerce/Checkout/CheckoutSummary', [
        
        ]);
    }

    public function index()
    {
        $test = Session::get('gbiz.gbiz.cart');
        return view('old1.e-commerce.checkout.checkout',[
            'test' => $test
        ]);
    }

    public function ptCheckoutIndex()
    {
        $membershipProducts = Products::where('status', 'active')->where('type', 'membership')->where('type_second', 'service')->with(['images'])->get();
        $cart = app(CartController::class)->getCartSessionDirect();

        return Inertia::render('ECommerce/Checkout/CheckoutPage', [
            'membershipProducts' => $membershipProducts,
            'cart' => $cart
        ]);
    }

    public function commitCartTransaction(Request $request)
    {
        //dd($request->value1);
        return $this->createStripeTransaction($request->value1);
    }

    public function createStripeTransaction($clientCartData){
        //TODO:Required to create cart transaction here; for stripe call back, as no access to session in api
        // get record created within last 30 minutes; https://stackoverflow.com/questions/48059596/get-data-created-x-minutes-ago-in-laravel

        //create shopping cart and stripe transactions
        $transactionTypeReturn = '';

        $transactionTypeReturn = app(PaymentTransactionLogicController::class)->createPaymentTransactionByTransactionType($clientCartData, 'stripe');

        return $transactionTypeReturn;

    }


    

    
}
