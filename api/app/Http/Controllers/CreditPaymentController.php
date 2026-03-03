<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\EphemeralKey;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CreditPaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $request->validate(rules: [
            'credits' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        Stripe::setApiKey(config(key: 'services.stripe.secret'));

        try {
            // Convert dollar amount to cents for Stripe
            $amountInCents = (int) ($request->price * 100);

            // Get or create customer
            $customer = Customer::create([
                'email' => Auth::user()->email,
                'name' => Auth::user()->name,
            ]);

            // Create ephemeral key for the customer
            $ephemeralKey = EphemeralKey::create(
                ['customer' => $customer->id],
                ['stripe_version' => $request->header(key: 'Stripe-Version')]
            );

            // Create payment intent with the actual amount
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                'customer' => $customer->id,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'credits_amount' => $request->credits,
                    'user_id' => Auth::id(),
                ],
            ]);

            Transaction::create([
                'user_id' => Auth::id(),
                'stripe_payment_intent_id' => $paymentIntent->id,
                'credits_amount' => $request->credits,
                'amount_paid' => $request->price,
                'currency' => 'usd',
                'status' => 'pending',
            ]);

            return response()->json([
                'paymentIntent' => $paymentIntent->client_secret,
                'ephemeralKey' => $ephemeralKey->secret,
                'customerId' => $customer->id,
                'publishableKey' => config(key: 'services.stripe.key'),
                'paymentIntentId' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            // Exception handling not shown
            Log::error('Error creating payment intent: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create payment intent',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function handlePaymentSuccess(Request $request)
    {
        try {
            $paymentIntentId = $request->input(key: 'payment_intent');

            // Get the transaction model instance
            $transaction = Transaction::where('stripe_payment_intent_id', $paymentIntentId)->first();

            if ($transaction && $transaction->user) {
                // Add credits to user
                $user = $transaction->user;
                $user->credits += $transaction->credits_amount;
                $user->save();

                return response()->json([
                    'success' => true,
                    'credits' => $user->credits, // Return the updated credit count
                    'credits_added' => $transaction->credits_amount,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Transaction not found or user not found',
            ], 404);

        } catch (\Exception $e) {
            // Exception handling code here
            Log::error(''. $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment success',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
