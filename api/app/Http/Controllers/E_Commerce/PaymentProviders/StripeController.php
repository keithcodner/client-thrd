<?php

namespace App\Http\Controllers\E_Commerce\PaymentProviders;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\NotificationsController;
use App\Http\Controllers\E_Commerce\CartController;
use App\Http\Controllers\E_Commerce\CheckoutController;
use App\Http\Controllers\E_Commerce\PaymentLogic\OrderController;
use App\Http\Controllers\E_Commerce\PaymentLogic\PaymentTransactionController;
use App\Http\Controllers\E_Commerce\PaymentLogic\PaymentTransactionLogicController;
use App\Http\Controllers\E_Commerce\PaymentLogic\MembershipController;
use App\Http\Controllers\E_Commerce\PaymentLogic\ShopperController;

use App\Models\PaymentTransactions\PaymentTransaction;
use App\Models\PaymentTransactions\PaymentTransactionPaymentTranscation;
use App\Models\PaymentTransactions\PaymentTransactionAddressBilling;
use App\Models\PaymentTransactions\PaymentTransactionAddressShipping;
use App\Models\PaymentTransactions\PaymentTransactionPaymentTranscationHistory;
use App\Models\PaymentTransactions\PaymentTransactionHistory;
use App\Models\PaymentTransactions\PaymentTransactionSettingProcessorStripe;
use App\Models\PaymentTransactions\PaymentTransactionOrder;
use App\Models\PaymentTransactions\PaymentTransactionMembership;
use App\Models\PaymentTransactions\PaymentTransactionPaymentProcessor;
use App\Models\PaymentTransactions\PaymentTransactionShopper;
use App\Models\PaymentTransactions\PaymentTransactionShoppingCart;
use App\Models\PaymentTransactions\PaymentTransactionTrackProductStat;
use App\Models\PaymentTransactions\PaymentTransactionInvoice;
use App\Models\PaymentTransactions\PaymentTransactionInvoiceItem;
use App\Models\PaymentTransactions\PaymentTransactionVAT;
use App\Models\PaymentTransactions\PaymentTransactionCountry;
use App\Models\PaymentTransactions\PaymentTransactionCoupon;
use App\Models\PaymentTransactions\PaymentTransactionCurrency;
use App\Models\PaymentTransactions\PaymentTransactionOrderItem;

use App\Models\CitiesCanada;
use App\Models\Settings\SiteSettings;
use App\Models\Local\CitiesUS;
use App\Models\Posts\JobPost;
use App\Models\User;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Stripe\TaxRate;
use Stripe\Checkout\Session as StripeCheckoutSession;

use Illuminate\Support\Facades\Redirect;
use Throwable;


class StripeController extends Controller
{
    public function __construct()
    {
        
    }


    public function index()
    {
        return view('old1.search.search');
    }

    /**
     * Check if Stripe webhook listener is running by checking for a health marker file
     * The stripe CLI should create/update this file when running
     */
    private function checkStripeWebhookHealth()
    {
        // Check if we're in local development (where stripe listen is required)
        $appEnv = config('app.env');
        
        // In production, webhooks come from Stripe servers, so always allow
        if ($appEnv === 'production' || $appEnv === 'local') {
            return true;
        }
        
        // In local/development, we need the stripe CLI listener running
        // Check if the stripe listen process is running (cross-platform)
        try {
            $output = [];
            $returnVar = 0;
            
            // Detect OS and use appropriate command
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows: Use tasklist
                exec('tasklist /FI "IMAGENAME eq stripe.exe" 2>NUL | find /I "stripe.exe"', $output, $returnVar);
            } else {
                // Linux/Unix/Debian: Use ps and grep
                exec('ps aux | grep "stripe listen" | grep -v grep', $output, $returnVar);
            }
            
            if ($returnVar === 0 && !empty($output)) {
                Log::info('Stripe webhook listener is running', ['os' => PHP_OS]);
                return true;
            }
        } catch (\Exception $e) {
            Log::warning('Could not check for stripe process: ' . $e->getMessage());
        }
        
        Log::error('Stripe webhook listener not detected. Please run: stripe listen --forward-to localhost:8000/api/stripeHook');
        return false;
    }

    /**
     * Public API endpoint to check webhook health status
     */
    public function checkWebhookStatus()
    {
        $isHealthy = $this->checkStripeWebhookHealth();
        
        return response()->json([
            'healthy' => $isHealthy,
            'message' => $isHealthy 
                ? 'Stripe webhook listener is running' 
                : 'Cannot contact Stripe Server. ',
            'environment' => config('app.env')
        ]);
    }

    public function createStripePaymentTransaction($stripe_array_data, $returnedCartTrxnData, $returnedOrderTrxnData, $clientCartData)
    {
        try {

            // ✅ CRITICAL: Verify Stripe webhook listener is running before processing payment
            if (!$this->checkStripeWebhookHealth()) {
                Log::error('Cannot contact Stripe Server! Cannot process payment.');
                throw new \Exception('Payment system unavailable: Cannot contact Stripe Server. ');
            }

            // ✅ Set the API key FIRST before any Stripe operations
            $STRIPE_SECRET = $_ENV['STRIPE_SECRET'];
            \Stripe\Stripe::setApiKey($STRIPE_SECRET);

            $addShipping = true;
            $shippingCost = 500; // $5.00
            $cartItems = (array)$stripe_array_data;
            $line_items = [];

            // ✅ Now it's safe to create tax rate
            $taxRate = \Stripe\TaxRate::create([
                'display_name' => 'Sales Tax',
                'description' => '13% HST',
                'jurisdiction' => 'Canada',
                'percentage' => 13.0,
                'inclusive' => false,
            ]);

            foreach($cartItems['cartProductList'] as $cartItem){
                try {
                    $temp_data = [
                        "quantity" => $cartItem['qty'],
                        "price_data" => [
                            "currency" => "usd",
                            "unit_amount" => (int)($cartItem['unitPrice'] * 100),
                            "product_data" => [
                                "name" => $cartItem['name']
                            ]
                        ],
                        // ✅ Tax rate must be in an array
                        "tax_rates" => [$taxRate->id]
                    ];
                    $line_items[] = $temp_data;
                } catch (Throwable $ex) {
                    // Log or handle the error
                }
            }

            //Determine whether to add shipping or not
            if($addShipping){
                $line_items[] = [
                    "quantity" => 1,
                    "price_data" => [
                        "currency" => "usd",
                        "unit_amount" => $shippingCost,
                        "product_data" => [
                            "name" => "Shipping Price"
                        ]
                    ],
                    //"tax_rates" => [$taxRate->id] // Optional
                ];
            }

            $stripCheckoutObject = [
                "mode" => "payment",
                "success_url" => "http://localhost:8000/ptCheckoutSuccess",
                //"cancel_url" => "http://localhost:8000/checkout",
                "cancel_url" => "http://localhost:8000/ptCheckout",
                "line_items" => $line_items,
                "client_reference_id" => $returnedOrderTrxnData->id,
                //"automatic_tax" => ["enabled" => true], // Not needed if using tax_rates

                // ✅ Prefill Email
                "customer_email" => $cartItems['data']['addressBilling']['email'],

                // Optionally collect shipping address
                "shipping_address_collection" => [
                    "allowed_countries" => ['US', 'CA']
                ],
                "metadata" => [
                    "user_id" => auth()->user()->id,
                    "cart_id" => $returnedCartTrxnData->id,
                    "order_id" => $returnedOrderTrxnData->id,
                    "shipping_amount" => $shippingCost,
                    "tax_rate" => $cartItems['givenTaxRate'],
                    "tax_amount" => $cartItems['taxAmount'],
                    "total_amount" => $cartItems['finalGrandTotalWithTaxAndShipping'],
                    "subtotal_amount" => $cartItems['subTotal'],
                    "qty" => $cartItems['qty']

                ]
            ];

            //Session::put('gbiz.gbiz.cart.id', ''); //clear first
            //Session::put('gbiz.gbiz.cart.id', $returnedCartTrxnData->id);

            $checkout_session = \Stripe\Checkout\Session::create($stripCheckoutObject);

            return [
                'checkout_url' => $checkout_session->url
            ];
        } catch (\Throwable $th) {
            Log::info($th);
        }

        
    }

    public function stripeHook_test(Request $request)
    {
        Log::info('Stripe Webhook Received:', $request->all());

        return response()->json(['status' => 'ok']);
    }

    //https://www.youtube.com/watch?v=b4Jz9UPAyI0&ab_channel=MattSocha
    public function stripeHook(Request $request){
        try {
            $event = '';
            $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
            $payload = @file_get_contents('php://input');

            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload, $sig_header, $endpoint_secret
                );
            } catch (\Stripe\Exception\SignatureVerificationException) {
                echo 'Webhook error while validating signature';
                http_response_code(400);
                exit();
            }

            


            if($event->type == 'checkout.session.completed'){
                $session = $event->data->object;

                Log::info($session);

                // Get metadata from Stripe session
                $user_id = $session->metadata->user_id ?? null;
                $order_id = $session->metadata->order_id ?? null;
                $cart_id = $session->metadata->cart_id ?? null;
                $job_post_id = $session->metadata->job_post_id ?? null;

                $updateJobPost = JobPost::where('id', $job_post_id)->update([
                    'status' => 'COMMITTED',
                ]);

                $updateOrder = PaymentTransactionOrder::where('id', $order_id)->update([
                    'payment_status' => 'paid',
                ]);

                // Send email to user after job post is committed
                dispatch(function () use ($user_id, $job_post_id, $session) {
                    try {
                        $user = User::find($user_id);
                        $jobPost = JobPost::find($job_post_id);
                        
                        if ($user && $jobPost) {
                            $amount = ($session->amount_total ?? 0) / 100; // Convert cents to dollars
                            $transactionId = $session->id ?? '';
                            
                            \Illuminate\Support\Facades\Mail::to($user->email)
                                ->send(new \App\Mail\JobPaymentSuccessMail($user, $jobPost, $amount, $transactionId));
                            
                            Log::info('Job payment success email sent', [
                                'user_id' => $user_id,
                                'job_post_id' => $job_post_id,
                                'transaction_id' => $transactionId
                            ]);
                        }
                    } catch (\Throwable $e) {
                        Log::error('Failed to send job payment success email: ' . $e->getMessage(), [
                            'user_id' => $user_id,
                            'job_post_id' => $job_post_id
                        ]);
                    }
                })->afterResponse();

                // Get amounts from Stripe session
                $amountTotal = $session->amount_total ?? 0;
                $sessionId = $session->id ?? '';
                $shipping_amount = $session->total_details->amount_shipping ?? 0;
                $tax_amount = $session->total_details->amount_tax ?? 0;
                $subtotal_amount = $session->amount_subtotal ?? 0;
                $total_amount = $session->amount_total ?? 0;
                $qty = $session->metadata->qty ?? 1;
                $tax_rate = $session->metadata->tax_rate ?? null;

                // Get the order object from your DB
                $createdTransactionOrderObject = PaymentTransactionOrder::where('id', $order_id)->first();

                // Update address from Stripe form if available
                if (isset($session->customer_details->address)) {
                    $address = $session->customer_details->address;
                    // Update billing address
                    PaymentTransactionAddressBilling::where('user_id', $user_id)
                        ->update([
                            'addr_street' => $address->line1 ?? null,
                            'addr_city' => $address->city ?? null,
                            'addr_state' => $address->state ?? null,
                            'addr_postal_code' => $address->postal_code ?? null,
                            'addr_country' => $address->country ?? null,
                        ]);
                    // Update shipping address
                    PaymentTransactionAddressShipping::where('user_id', $user_id)
                        ->update([
                            'addr_street' => $address->line1 ?? null,
                            'addr_city' => $address->city ?? null,
                            'addr_state' => $address->state ?? null,
                            'addr_postal_code' => $address->postal_code ?? null,
                            'addr_country' => $address->country ?? null,
                        ]);
                }

                // Record payment transaction
                app(PaymentTransactionController::class)
                    ->createPaymentTransaction(
                        $createdTransactionOrderObject,
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
                        $qty
                    );

                \Log::info("Stripe payment transaction recorded for order_id: $order_id");
            }

            return response()->json(["status" => 'ok']);
        } catch (\Throwable $th) {
            \Log::info($th);
            return response()->json(["status" => 'error', "message" => $th->getMessage()]);
        }

        
    }

    public function test(Request $request){

    }

    public function createStripeSession(Request $request)
    {
        // ✅ CRITICAL: Check webhook health BEFORE creating any Stripe session
        if (!$this->checkStripeWebhookHealth()) {
            Log::error('Stripe webhook listener not running - blocking payment session creation', [
                'user_id' => auth()->user()->id ?? null,
                'environment' => config('app.env')
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Payment system is currently unavailable. Please ensure the Stripe webhook listener is running.',
                'technical_details' => config('app.env') === 'production' 
                    ? 'Service temporarily unavailable' 
                    : 'Run: stripe listen --forward-to localhost:8000/api/stripeHook'
            ], 503);
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $cart = $request->input('cart', []);
        $currency = 'usd';

        // Build clientCartData from request/cart (adapt as needed)
        $clientCartData = [
            'cartProductList' => $cart,
            'data' => [
                'addressBilling' => [
                    'nameOnCard' => auth()->user()->firstname ?? 'Unknown', // fallback if missing
                    'email' => auth()->user()->email,
                ],
                'addressShipping' => [
                    'nameOnCard' => auth()->user()->firstname ?? 'Unknown',
                    'email' => auth()->user()->email,
                ],
            ],
        ];

        // Create billing and shipping address transactions (replace with your actual logic)
        $returnedAddrBillTrxnData = PaymentTransactionAddressBilling::create([
            'user_id' => auth()->user()->id,
            'name' => auth()->user()->firstname,
            'email' => auth()->user()->email,
        ]);
        $returnedAddrShipTrxnData = PaymentTransactionAddressShipping::create([
            'user_id' => auth()->user()->id,
            'name' => auth()->user()->firstname,
            'email' => auth()->user()->email,
        ]);

        // Create shopper transaction
        $returnedShopperTrxnData = app(ShopperController::class)
            ->createShopperTransaction($clientCartData, $returnedAddrBillTrxnData, $returnedAddrShipTrxnData);

        // Create cart transaction (replace with your actual cart logic)
        $returnedCartTrxnData = PaymentTransactionShoppingCart::create([
            'user_id' => auth()->user()->id,
            // ...other cart fields...
        ]);

        // Create order transaction
        $returnedOrderTrxnData = app(OrderController::class)
            ->createOrderTransaction($returnedCartTrxnData, $clientCartData, $returnedShopperTrxnData);
        
        $jobPost = JobPost::findOrFail($request->input('job_post_id'));
        $jobPost->order_id = $returnedOrderTrxnData->id;
        $jobPost->save();

        //job_post_id

        // Build Stripe line items
        $line_items = [];
        foreach ($cart as $item) {
            $productData = [
                'name' => $item['lineItemName'] ?? $item['name'] ?? 'Unknown Product', // fallback if missing
            ];
            if (!empty($item['benefit'])) {
                $productData['description'] = $item['benefit'];
            }
            $line_items[] = [
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => (int)($item['price'] * 100),
                    'product_data' => $productData,
                ],
                'quantity' => 1,
            ];
        }

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => url('/purchase-success'),
            'cancel_url' => url('/postgig'),
            // Optionally pass metadata for later reference
            'metadata' => [
                'order_id' => $returnedOrderTrxnData->id,
                'cart_id' => $returnedCartTrxnData->id,
                'user_id' => auth()->user()->id,
                'job_post_id' => $request->input('job_post_id'),
            ],
        ]);

        return response()->json([
            'checkout_url' => $checkout_session->url
        ]);
    }

    /**
     * Handle Stripe webhooks to mark job posts as committed after payment
     */
    public function handleWebhook(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the checkout.session.completed event
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            
            // Get job_post_id from metadata
            $jobPostId = $session->metadata->job_post_id ?? null;
            $orderId = $session->metadata->order_id ?? null;
            
            if ($jobPostId) {
                // Mark the job post as COMMITTED
                $job = \App\Models\Posts\JobPost::find($jobPostId);
                if ($job && $job->status === 'DRAFT') {
                    $job->status = 'COMMITTED';
                    $job->order_id = $orderId;
                    $job->save();
                    
                    \Log::info('Job post marked as COMMITTED after payment', [
                        'job_id' => $jobPostId,
                        'session_id' => $session->id
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
