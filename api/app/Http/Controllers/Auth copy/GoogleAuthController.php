<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Support\SiteHelperController;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists with this Google ID
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if (!$user) {
                // Check if user exists with this email
                $user = User::where('email', $googleUser->getEmail())->first();
                
                if ($user) {
                    // Link Google account to existing user
                    $user->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                } else {
                    // Create new user
                    $nameParts = explode(' ', $googleUser->getName(), 2);
                    $firstName = $nameParts[0] ?? '';
                    $lastName = $nameParts[1] ?? '';
                    
                    $user = User::create([
                        'role_id' => '2', // Regular user role
                        'firstname' => $firstName,
                        'lastname' => $lastName,
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => app(SiteHelperController::class)->getRandomProNetworkBackgroundProfileImage(),
                        'password' => Hash::make(Str::random(24)), // Random password
                        'email_verified_at' => now(), // Auto-verify email for Google users
                        'profile_photo_path' => $googleUser->getAvatar(),
                    ]);
                    
                    // Send welcome email to new user
                    Mail::to($user->email)->send(new WelcomeMail($user));
                }
            }
            
            // Log the user in
            Auth::login($user, true);
            
            // Redirect to profile or intended page
            return redirect()->intended('/profile');
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Unable to login with Google. Please try again.');
        }
    }
}
