<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\User;
use App\Models\Posts\JobPost;
use App\Models\PaymentTransactions\PaymentTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\Refund;

class ManageStripeRefundController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
        Paginator::useBootstrap();
    }

    public function index(Request $request)
    {
        // Build query for users who have job posts
        $usersQuery = User::whereHas('jobPosts', function($query) {
            $query->where('status', 'COMMITTED');
        });

        // Apply search filters
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $usersQuery->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%");
            });
        }

        // Get paginated users
        $users = $usersQuery->orderBy('created_at', 'desc')->paginate(15);

        // Attach job posts and payment data to each user
        $users->getCollection()->transform(function ($user) {
            // Get all committed job posts for this user
            $jobPosts = JobPost::where('author_id', $user->id)
                ->where('status', 'COMMITTED')
                ->orderBy('created_at', 'desc')
                ->get();

            // Get all payments for this user
            $allPayments = PaymentTransaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->get();

            // Attach payments to each job post (you can filter by order_id if available)
            $jobPosts->each(function($jobPost) use ($allPayments) {
                // If job posts have order_id, filter payments by that
                if ($jobPost->order_id) {
                    $jobPost->payments = $allPayments->where('order_id', $jobPost->order_id)->values();
                } else {
                    // Otherwise show all user payments (admin can determine which belong to this post)
                    $jobPost->payments = collect([]);
                }
            });

            $user->job_posts = $jobPosts;
            $user->total_job_posts = $jobPosts->count();
            $user->all_payments = $allPayments;
            $user->total_paid = $allPayments->sum('amount');
            
            return $user;
        });

        return Inertia::render('Admin/Payment/Stripe/AdminStripeRefundManagement', [
            'users' => $users,
            'filters' => [
                'search' => $request->search
            ]
        ]);
    }

    public function processRefund(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:trxn_payment_transaction,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $payment = PaymentTransaction::findOrFail($request->payment_id);

            // Check if payment has a Stripe transaction number
            if (!$payment->txn_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Stripe transaction number found for this payment.'
                ], 400);
            }

            // Initialize Stripe
            Stripe::setApiKey(config('services.stripe.secret'));

            // Convert amount to cents (Stripe uses cents)
            $refundAmount = (int)($request->amount * 100);

            // Determine the payment intent ID based on what we have stored in txn_number
            $paymentIntentId = null;
            
            // Check if txn_number is a Checkout Session (cs_), PaymentIntent (pi_), or Charge (ch_)
            if (str_starts_with($payment->txn_number, 'cs_')) {
                // It's a Checkout Session - need to retrieve the PaymentIntent from it
                $checkoutSession = \Stripe\Checkout\Session::retrieve($payment->txn_number);
                $paymentIntentId = $checkoutSession->payment_intent;
            } elseif (str_starts_with($payment->txn_number, 'pi_')) {
                // Already a PaymentIntent ID
                $paymentIntentId = $payment->txn_number;
            } elseif (str_starts_with($payment->txn_number, 'ch_')) {
                // It's a Charge ID - can refund directly
                $paymentIntentId = $payment->txn_number;
            } else {
                // Unknown format - try as-is
                $paymentIntentId = $payment->txn_number;
            }

            // Create refund in Stripe
            // Stripe only accepts: duplicate, fraudulent, or requested_by_customer
            $refundParams = [
                'amount' => $refundAmount,
                'reason' => 'requested_by_customer', // Always use this for admin refunds
                'metadata' => [
                    'admin_user' => Auth::id(),
                    'original_payment_id' => $payment->id,
                    'custom_reason' => $request->reason ?? 'No reason provided' // Store custom reason in metadata
                ]
            ];

            // Use payment_intent for pi_ IDs, charge for ch_ IDs
            if (str_starts_with($paymentIntentId, 'pi_')) {
                $refundParams['payment_intent'] = $paymentIntentId;
            } else {
                $refundParams['charge'] = $paymentIntentId;
            }

            $refund = Refund::create($refundParams);

            // Update payment status
            $payment->update([
                'status' => 'refunded',
                'details' => json_encode([
                    'refund_id' => $refund->id,
                    'refund_amount' => $request->amount,
                    'refund_date' => now(),
                    'refund_reason' => $request->reason
                ])
            ]);

            // Find and archive the associated job post
            if ($payment->order_id) {
                $jobPost = JobPost::where('order_id', $payment->order_id)->first();
                if ($jobPost) {
                    $jobPost->update([
                        'status' => 'ARCHIVED',
                        'expires_at' => now()->subDay() // Set to yesterday
                    ]);
                }
            } else {
                // If no order_id, try to find job post by user_id and approximate date
                // This is a fallback - ideally all payments should have order_id
                $jobPost = JobPost::where('author_id', $payment->user_id)
                    ->where('status', 'COMMITTED')
                    ->whereBetween('created_at', [
                        $payment->created_at->subHours(24),
                        $payment->created_at->addHours(24)
                    ])
                    ->first();
                
                if ($jobPost) {
                    $jobPost->update([
                        'status' => 'ARCHIVED',
                        'expires_at' => now()->subDay() // Set to yesterday
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully and job post archived.',
                'refund_id' => $refund->id
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing refund: ' . $e->getMessage()
            ], 500);
        }
    }
}
