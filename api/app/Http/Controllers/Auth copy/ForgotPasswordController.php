<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\NewsletterEmail;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\SiteHelperController;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['guest']);
    }
    
    public function index()
    {
        return view('old1.auth.forgotpassword');
    }

    public function newsletterEmail(Request $request)
    {
        NewsletterEmail::create([
            'email' => $request->value1
        ]);

        return 'You are now subscribed to our newsletter!';
    }

    public function sendChangePasswordEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        
        $email = $request->input('email');
        
        // Check if email exists
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            // Don't reveal if user exists or not for security
            return back()->with('status', 'If an account exists with that email, you will receive a password reset link shortly.');
        }

        // Check if token already exists
        if (!empty($user->change_PasswordToken)) {
            return back()->with('error', 'A password reset link was already sent. Please check your email or wait before requesting another.');
        }

        // Generate token
        $serverToken = uniqid().'-'.uniqid().'-'.uniqid().'-'.uniqid();
        
        // Update user with token
        $user->update([
            'change_PasswordToken' => $serverToken
        ]);

        // Generate reset URL
        $resetUrl = url('/password/reset/'.$serverToken);

        // Send forgot password email asynchronously
        dispatch(function () use ($user, $resetUrl) {
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->send(new \App\Mail\ForgotPasswordMail($user, $resetUrl));
                
                \Illuminate\Support\Facades\Log::info('Password reset email sent', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send password reset email: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }
        })->afterResponse();

        return back()->with('success', 'Password reset email sent! Please check your inbox for instructions.');
    }

    /**
     * Show the password reset form
     */
    public function showResetForm($token)
    {
        $user = User::where('change_PasswordToken', $token)->first();
        
        if (!$user) {
            return redirect('/forgot-password')->with('error', 'Invalid or expired reset token.');
        }

        $image = \App\Models\Settings\SiteSettings::first();
        
        return \Inertia\Inertia::render('Auth/ResetPassword/ResetPassword', [
            'token' => $token,
            'email' => $user->email,
            'image' => $image
        ]);
    }

    /**
     * Reset the password
     */
    public function reset(Request $request)
    {
        // Validate with password strength requirements matching registration
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain at least one special character
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
        ]);

        $user = User::where('email', $request->email)
            ->where('change_PasswordToken', $request->token)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid token or email address.']);
        }

        // Update password and clear token
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'change_PasswordToken' => null,
        ]);

        return redirect('/login')->with('success', 'Your password has been reset successfully! You can now login with your new password.');
    }

    
}
