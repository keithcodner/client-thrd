<?php

namespace App\Http\Controllers\E_Commerce;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\NotificationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

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

    Models\Posts\JobPost,
    Models\User
};

use \Log;
use Inertia\Inertia;

class ManagePurchaseController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    /**
     * Get the latest job post ID for the authenticated user
     */
    public function getLatestJobPostId(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $latestJobPost = JobPost::where('user_id', $user->id)
            ->where('status', 'COMMITTED')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestJobPost) {
            return response()->json(['jobPostId' => $latestJobPost->id]);
        }

        return response()->json(['jobPostId' => null]);
    }

    public function ptViewManagePurchaseIndex()
    {
        return Inertia::render('ECommerce/Cart/CartPage', [
            'getCart' => $this->getCartSessionDirect()
        ]);
    }

    public function ptViewReceiptIndex()
    {
        $data = $this->getUserPurchaseHistoryByUser(auth()->user()->id);
        return Inertia::render('ECommerce/Purchases/ViewPurchases', [
            'orderAndOrderDetails' => $data
        ]);
    }

    public function ptViewOrderIndex()
    {
        return Inertia::render('ECommerce/Cart/CartPage', [
            'getCart' => $this->getCartSessionDirect()
        ]);
    }

    public function testProducts(Request $request)
    {
    }

    public function getUserPurchaseHistoryByUser($userId)
    {
        $purchaseHistory = PaymentTransactionOrder::where('status', 'completed')->where('payment_status', 'paid')->where('user_id', $userId)->with([ 'cart', 'payment_transaction', 'payment_transaction_history', 'order_history', 'order_items', 'shopper', 'shipping', 'billing'])->limit(100)->orderBy('created_at','desc')->get();

        return $purchaseHistory;
    }

    public function getUserPurchaseHistoryByPurchase($userId)
    {
        $purchaseHistory = PaymentTransactionOrder::where('status', 'completed')->where('payment_status', 'paid')->where('user_id', $userId)->with([ 'cart', 'payment_transaction', 'payment_transaction_history', 'order_history', 'order_items', 'shopper', 'shipping', 'billing'])->limit(100)->orderBy('created_at','desc')->get();

        return $purchaseHistory;
    }

    public function getUserPurchaseHistoryByOrderId($orderId)
    {
      
    }

    /**
     * Get job post purchase details for a specific job post
     */
    public function getJobPostPurchaseDetails($jobPostId)
    {
        $jobPost = JobPost::findOrFail($jobPostId);
        
        Log::info('📋 getJobPostPurchaseDetails - JobPost:', [
            'id' => $jobPost->id,
            'order_id' => $jobPost->order_id,
            'status' => $jobPost->status
        ]);
        
        // Get the order for this job post
        $purchase = null;
        if ($jobPost->order_id) {
            $purchase = PaymentTransactionOrder::where('user_id', Auth::id())
                ->where('id', $jobPost->order_id)
                ->where('status', 'completed')
                ->where('payment_status', 'paid')
                ->with(['order_items', 'payment_transaction', 'shopper', 'billing', 'shipping'])
                ->first();
            
            Log::info('💳 Purchase found:', $purchase ? ['id' => $purchase->id, 'pay_amount' => $purchase->pay_amount] : ['status' => 'null']);
        } else {
            Log::warning('⚠️ JobPost has no order_id - may be free tier or unpaid');
        }
        
        return [
            'jobPost' => $jobPost,
            'purchase' => $purchase,
        ];
    }

    /**
     * View receipt for a specific job post purchase
     */
    public function viewJobPostReceipt($jobPostId)
    {
        $data = $this->getJobPostPurchaseDetails($jobPostId);
        
        return Inertia::render('ECommerce/Purchases/ViewPurchases', [
            'jobPostPurchase' => $data,
            'orderAndOrderDetails' => $data['purchase'] ? [$data['purchase']] : []
        ]);
    }
}
