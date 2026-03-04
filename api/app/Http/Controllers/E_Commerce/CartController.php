<?php

namespace App\Http\Controllers\E_Commerce;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\NotificationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\{
    Models\PaymentTransactions\PaymentTransaction,
    Models\PaymentTransactions\PaymentTransactionPaymentTranscation,
    Models\PaymentTransactions\PaymentTransactionAddressBilling,
    Models\PaymentTransactions\PaymentTransactionAddressShipping,
    Models\PaymentTransactions\PaymentTransactionPaymentTranscationHistory,
    Models\PaymentTransactions\PaymentTransactionHistory,
    Models\PaymentTransactions\PaymentTransactionSettingProcessorStripe,
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

use Inertia\Inertia;
class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('old1.e-commerce.checkout.cart');
    }

    public function ptCartIndex()
    {
        $test = Session::get('gbiz.gbiz.cart');
        return Inertia::render('ECommerce/Cart/CartPage', [
            'getCart' => $this->getCartSessionDirect()
        ]);
    }

    public function testProducts(Request $request)
    {
        $test = $this->clearCartSession();
        
        $products = PaymentTransactionProduct::where('status', 'active')->get();

        return view('old1.e-commerce.test.testProducts', [
            'test' => $test,
            'products' => $products,
        ]);
    }

    /*
        Supppose you have following array structure inside your session

        $user = [
            "name" => "Joe",
            "age"  => 23
        ]

        session()->put('user',$user);

        //updating the age in session
        session()->put('user.age',49);
        if your session array is n-arrays deep then use the dot (.) followed by key names to reach to the nth value or array, like session->put('user.comments.likes',$likes)
    
    */

    public function storeCartSession(Request $request)
    {
        Session::put('gbiz.gbiz.cart', $request->value1);

        return Session::get('gbiz.gbiz.cart');
        //return $request->value1;
    }

    public function getCartSession(Request $request)
    {
        return [
            'cart' => Session::get('gbiz.gbiz.cart')
        ];
        //return $request->value1;
    }

    public function getCartSessionDirect()
    {
        return [
            'cart' => Session::get('gbiz.gbiz.cart')
        ];
        //return $request->value1;
    }

    public function clearCartSession(){
        $test = '';
        Session::forget('gbiz');
        if(!Session::has('gbiz')){
           Session::put('gbiz', [
            'gbiz' => [
                'cart' => [

                ],
                'data' => [

                ]
            ]
           ]);
           $test = Session::get('gbiz');
        }

        return $test;
    }

    public function createShoppingCartTransaction($cartObj)
    {
        $test = '';
        $shoppingCartPaymentTransaction = PaymentTransactionShoppingCart::create([
            "user_id" => auth()->user()->id,
            'status' => 'active', //default:active
            'cart_data' => json_encode($cartObj),
            'expire_threshold' => '30', //default: 30 mins
        ]); 

        return $shoppingCartPaymentTransaction;
    }
}
