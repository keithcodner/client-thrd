<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Settings\SiteSettings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['guest']);
    }
    
    public function index()
    {
        return view('old1.auth.login');
    }

    public function getLocation(Request $request){
        $userData = User::where('id', auth()->user()->id)->first();

        // return [ 
        //     'city' => $userData->user_city,
        //     'lat' => $userData->user_lat,
        //     'lon' => $userData->user_long,
        // ];

        return 'test';
    }

    public function store(Request $request)
    {
        //Validated email regex
        // $this->validate($request, [
        //     'email' => 'required|email',
        // ]);

        //Set Initial Login Variables
        $user = User::where('email', $request->email)->first();
        $login_settings = SiteSettings::where('name', 'login_try_attempts')->first();
        $login_failed_attempt_count = 0;
                

        if(
            (User::where('email', $request->email)->where('status', 'active')->exists()) &&
            (Carbon::now() > new Carbon($user->suspend_reactive))
        ){
            
            if(!auth()->attempt($request->only('email', 'password'), $request->remember)){

                //Determine if password threshold defined in settings is met (from last time to try to now account is locked)
                if($user->password_try >= $login_settings->value){

                    //give lock status and suspend account
                    $suspend_end_date = Carbon::now();
                    $suspend_end_date->addDays(1);
                    $update = User::where('email', $request->email)->update([
                        "status" => 'locked',
                        "suspend_reactive" => $suspend_end_date,
                    ]);

                    return back()->with('Account_locked', 'Too many failed attempts. Your account has been locked for the next 24 hours. ');
                }else{
                    if(($user->password_try == null) || ($user->password_try == 0)){
                        $login_failed_attempt_count = 1;
                    }else{
                        $login_failed_attempt_count = $user->password_try;
                        $login_failed_attempt_count++;
                    }
                    
                    $update = User::where('email', $request->email)->update([
                        'password_try' => $login_failed_attempt_count,
                    ]);
    
                    return back()->with('status', 'Invalid Login Details');
                }
            }
    
            //Get when user is loggedin
            User::where('id', auth()->user()->id)->update([
                'last_login' => Carbon::now(), 
                'lastLoginIP' => $request->ip(), 
                'status' => 'active',
                'password_try' => '0',
            ]);

            //Create  Session
            Session::put('site', [
                'cart' => []
            ]);
    
            return redirect()->route('legal');

        }else if(User::where('email', $request->email)->where('status', 'inactive')->exists()){
            return back()->with('Account_inactive', 'Your account is not active. ');
        }else if(
            (User::where('email', $request->email)->where('status', 'inactive')->exists()) &&
            Carbon::now() < new Carbon($user->suspend_reactive)
        ){
            return back()->with('Account_suspend', 'Your account is suspended until further notice.');
        }else if(
            (User::where('email', $request->email)->where('status', 'locked')->exists()) &&
            ($user->password_try >= $login_settings->value)
        ){
            //Determine if password threshold defined in settings is met (attemps after the account is already locked)

            if(
                (User::where('email', $request->email)->where('status', 'locked')->exists()) &&
                Carbon::now() < new Carbon($user->suspend_reactive) 
            ){

                return back()->with('Account_locked', 'Too many failed attempts. Your account has been locked for the next 24 hours. ');

            }else if(
                (User::where('email', $request->email)->where('status', 'locked')->exists()) &&
                ($user->password_try >= $login_settings->value) &&
                (Carbon::now() > new Carbon($user->suspend_reactive))
            ){
                
                if(!auth()->attempt($request->only('email', 'password'), $request->remember)){
                    $update = User::where('email', $request->email)->update([
                        'password_try' => '0',
                        'status' => 'active',
                    ]);

                    return back()->with('status', 'Invalid Login Details');
                }

                //Get when user is loggedin
                User::where('id', auth()->user()->id)->update([
                    'last_login' => Carbon::now(), 
                    'lastLoginIP' => $request->ip(), 
                    'status' => 'active',
                    'password_try' => '0',
                ]);

                
            
                return redirect()->route('dashboard');
            }
        }else{
            return back()->with('status', 'Invalid Login Details');
        }

        
    }

    
}
