<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailMail;
use App\Mail\ResendVerifyEmailMail;

class EmailVerificationController extends Controller
{
    /**
     * Verify the user's email address.
     */
    public function verify(Request $request, $token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return \Inertia\Inertia::render('Auth/VerifyEmail/VerifyEmail', [
                'success' => false,
                'message' => 'Invalid or expired verification token.'
            ]);
        }

        if ($user->email_verified_at) {
            return \Inertia\Inertia::render('Auth/VerifyEmail/VerifyEmail', [
                'success' => true,
                'message' => 'Your email has already been verified.',
                'alreadyVerified' => true
            ]);
        }

        // Mark email as verified
        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_IsVerified' => 1
        ]);

        // Log the user in if they're not already
        if (!Auth::check()) {
            Auth::login($user);
        }

        return redirect('/')->with('verified', true);
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return back()->with('error', 'You must be logged in to resend verification email.');
        }

        if ($user->email_verified_at) {
            return back()->with('error', 'Your email is already verified.');
        }

        // Generate new verification token
        $token = bin2hex(random_bytes(32));
        
        $user->update([
            'email_verification_token' => $token
        ]);

        // Generate verification URL
        $verificationUrl = url('/email/verify/' . $token);

        // Send verification email asynchronously
        dispatch(function () use ($user, $verificationUrl) {
            try {
                Mail::to($user->email)
                    ->send(new ResendVerifyEmailMail($user, $verificationUrl));
                
                \Illuminate\Support\Facades\Log::info('Resend verification email sent', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to resend verification email: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }
        })->afterResponse();

        return back()->with('status', 'Verification email sent! Please check your inbox.');
    }

    /**
     * Show notice that email verification is required.
     */
    public function notice()
    {
        $image = \App\Models\Settings\SiteSettings::first();
        
        return \Inertia\Inertia::render('Auth/VerifyEmail/VerifyEmailNotice', [
            'image' => $image
        ]);
    }
}
