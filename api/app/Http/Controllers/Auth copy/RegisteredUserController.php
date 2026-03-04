<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Support\SiteHelperController;
use App\Mail\WelcomeMail;
use App\Mail\VerifyEmailMail;
use App\Models\ProNetwork\ProNetworkConnections;
use App\Models\ProNetwork\ProNetworkGroup;
use App\Models\ProNetwork\ProNetworkUserProfile;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            //'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generate email verification token
        $verificationToken = bin2hex(random_bytes(32));
        
        $user = User::create([
            'firstname' => $request->first_name,
            'lastname' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'avatar' => app(SiteHelperController::class)->getRandomProNetworkBackgroundProfileImage(),
            'role_id' => '2', // Regular users get role_id 2
            'type' => 'base_user', // Set type as base_user for regular users
            'alpha_num_id' => $this->createAlphaNumericId(),
            'password' => Hash::make($request->password),
            'email_verification_token' => $verificationToken,
        ]);

        //Create MyNetwork Group profile for this user
        $pronetwork_group_profile = ProNetworkGroup::create([
            'target_id' => $user->id, //matches user_id from MyNetworkUserProfile
            'status' => 'active',
            'type' => 'person'
        ]);

        //Create MyNetwork profile for this user
        $pronetwork_profile = ProNetworkUserProfile::create([
            'user_id' => $user->id, //matches target_id from MyNetworkGroup
            'header_image_id' => '0',
            'connections_count' => '0',
            'type' => 'person',
            'status' => 'active'
        ]);

        //Create MyNetwork connection profile for this user (user connects to themselves initially)
        $pronetwork_connection = ProNetworkConnections::create([
            'net_group_id' => $pronetwork_group_profile->id, //not the target_id
            'an_id' => app(SiteHelperController::class)->createAlphaNumericId(),
            'initiator_user_id' => $user->id,
            'accepter_user_id' => $user->id,
            'type' => 'connection',
            'isConnected' => 'true',
            'status' => 'active',
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Generate verification URL
        $verificationUrl = url('/email/verify/' . $verificationToken);

        // Send welcome and verification emails asynchronously (after response is sent)
        dispatch(function () use ($user, $verificationUrl) {
            try {
                // Send welcome email
                Mail::to($user->email)->send(new WelcomeMail($user));
                
                // Send verification email
                Mail::to($user->email)->send(new VerifyEmailMail($user, $verificationUrl));
                
                Log::info('Welcome and verification emails sent to ' . $user->email);
            } catch (\Exception $e) {
                Log::error('Failed to send emails to ' . $user->email . ': ' . $e->getMessage());
            }
        })->afterResponse();

        return redirect(RouteServiceProvider::HOME_TEMP);
    }

    public function createAlphaNumericId(){
        return uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;
    }
    
    /**
     * Check if username is available
     */
    public function checkUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:8|max:255',
        ]);
        
        $username = $request->username;
        $exists = User::where('username', $username)->exists();
        
        return response()->json([
            'available' => !$exists,
            'username' => $username
        ]);
    }
}
