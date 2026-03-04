<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\NotificationsController;
use App\Http\Controllers\Core\RankingController;
use App\Http\Controllers\Core\SiteHelperController;
use App\Mail\SiteMailServer;
use App\Models\Conversation\ConversationCategory;
use App\Models\Ranking\Ranking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['guest']);
    }
    
    public function index()
    {
        return view('old1.auth.register');
    }

    public function store(Request $request)
    {
        //Validate
        $this->validate($request, [
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
            'username' => 'required|max:50',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed',
        ]);

        //Create User
        User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'alpha_num_id' => $request->user_an_id,
            'user_settings' => '[{"id":1,"name":"enable_sidebar_minified_as_default","value":"off"},{"id":2,"name":"enable_pronetwork_profile","value":"off"},{"id":3,"name":"auto_accept_trade","value":"off"},{"id":4,"name":"auto_decline_trade","value":"off"},{"id":5,"name":"default_trade_list_or_grid_view","value":"grid"},{"id":6,"name":"site_notifications","value":"on"},{"id":7,"name":"enable_help_text","value":"on"},{"id":8,"name":"hide_wishlist","value":"off"},{"id":9,"name":"locale","value":"en"},{"id":10,"name":"location","value":"on"},{"id":11,"name":"notifications_on_trades","value":"on"},{"id":12,"name":"notification_sounds","value":"off"},{"id":13,"name":"preferred_language","value":"off"},{"id":14,"name":"trade_dashboard_light_or_dark_theme","value":"off"}]',
            'password' => Hash::make($request->password),
        ]);

        //Get User to get ID
        $this_user = User::where('alpha_num_id', $request->user_an_id)->first();

        //Create Ranking Record 
        Ranking::create([
            'user_id' => $this_user->id,
            'rank_group_id' => 16,
            'rank_status' => 'active',
            'rank_score' => 1100,
        ]);

        //Create Conversation Category (default and arcives) For User
        ConversationCategory::create([
            'owner_user_id' => $this_user->id,
            'category_an_id'  => app(SiteHelperController::class)->createAlphaNumericId(),
            'category_name'  => 'Default',
            'category_description'  =>'default',
            'category_expand_state'  =>'closed',
            'category_status'  => 'active',
            'category_type'  => 'default',
        ]);

        ConversationCategory::create([
            'owner_user_id' => $this_user->id,
            'category_an_id'  => app(SiteHelperController::class)->createAlphaNumericId(),
            'category_name'  => 'Archived',
            'category_description'  =>'default',
            'category_expand_state'  =>'closed',
            'category_status'  => 'active',
            'category_type'  => 'default',
        ]);

        //Login to account
        Auth::attempt($request->only('email', 'password'), true); 

        //Send email
        $details = [
            'title' => 'Welcome to GigBizness',
            'body' => 'Thank you for registering.',
            'firstname' => $request->firstname,
        ];
        Mail::to($request->email)->send(new SiteMailServer($details));

        //Give the user 1000 rank points for signing up
        //app(RankingController::class)->rankTransactionCommitDraft("6", $this_user->id);
        app(NotificationsController::class)->generateSiteNotification(
            'Congratulations! Your Rank has increased.',
            'Congratulations! Your Rank has increased. you just got 1000 points just for signing up.  Continue on this route and build up your rank and profile!',
            'rank_message',
            $this_user->id, //user id
            0, //from id
            "off",
        );
        
        //Go to the Dashboard
        return redirect()->route('dashboard');

    }

    public function checkEmailAvailabilityReg(Request $request)
    {
        $isValid = 'true|This Email is a Valid Email to use!';
        $validator = Validator::make($request->all(), [
            'value1' => 'required|email|max:255',
        ]);

        $db_email = User::where('email', $request->value1)->pluck('email');
        
        if($validator->fails()){
            $isValid = 'false|Email is not valid! Please enter a valid email.';
        }else{
            //if (trim($db_email[0]) == trim($request->value1)) {
            if (User::where('email', $request->value1)->exists()) {
                $isValid = 'false|Email already exists! Please enter a different email.';
            }
        }

        return $isValid;
    }

    public function checkUsernameAvailabilityReg(Request $request)
    {
        $isValid = 'true|This username is a Valid username to use!';
        $validator = Validator::make($request->all(), [
            'value1' => 'required|string|regex:/\w*$/|max:50|unique:users,username',
        ]);

        $db_username = User::where('username', $request->value1)->pluck('username');
        
        if($validator->fails()){
            $isValid = 'false|Username is not valid! Please enter a valid username.';
        }else{
            //if (trim($db_email[0]) == trim($request->value1)) {
            if (User::where('username', $request->value1)->exists()) {
                $isValid = 'false|Username already exists! Please enter a different username.';
            }
        }

        return $isValid;
    }
}
