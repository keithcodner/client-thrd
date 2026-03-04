<?php

namespace App\Http\Controllers\E_Commerce\Products;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\NotificationsController;
use App\Http\Controllers\E_Commerce\CartController;
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

    Models\Shopping\Products,

    Models\User
};

use Inertia\Inertia;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('old1.e-commerce.checkout.cart');
    }

    public function ptAddMembershipIndex()
    {
        app(CartController::class)->clearCartSession();
        $membershipProducts = Products::where('status', 'active')->where('type', 'membership')->where('type_second', 'service')->with(['images'])->get();

        $cart = app(CartController::class)->getCartSessionDirect();

        //dd($membershipProducts);

        return Inertia::render('ECommerce/Product/ProductTierPage', [
            'membershipProducts' => $membershipProducts,
            'cart' => $cart
        ]);
    }

}
