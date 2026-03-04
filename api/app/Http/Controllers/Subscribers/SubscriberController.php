<?php

namespace App\Http\Controllers\Subscribers;

use App\Http\Controllers\Controller;
use App\Models\Subscribers\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SubscriberController extends Controller
{
    /**
     * Store a new newsletter subscriber.
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:50|unique:subscribers,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('email'),
            ], 422);
        }

        try {
            $subscriber = Subscriber::create([
                'email' => $request->email,
                'status' => 'active',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing to our newsletter!',
                'data' => $subscriber,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Newsletter subscription error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your subscription. Please try again.',
            ], 500);
        }
    }

    /**
     * Unsubscribe a user from the newsletter.
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('email'),
            ], 422);
        }

        try {
            $subscriber = Subscriber::where('email', $request->email)->first();

            if (!$subscriber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not found in our subscriber list.',
                ], 404);
            }

            $subscriber->update(['status' => 'unsubscribed']);

            return response()->json([
                'success' => true,
                'message' => 'You have been successfully unsubscribed.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Newsletter unsubscribe error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again.',
            ], 500);
        }
    }

    /**
     * Get all active subscribers (admin only).
     */
    public function index()
    {
        try {
            $subscribers = Subscriber::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subscribers,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Fetch subscribers error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching subscribers.',
            ], 500);
        }
    }
}
