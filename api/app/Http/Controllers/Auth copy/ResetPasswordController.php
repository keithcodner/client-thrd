<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Core\SiteHelperController;

class ResetPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware(['guest']);
    }
    
    public function index(Request $request)
    {
        $token = $request->token;
        if(User::where('change_PasswordToken', $request->token)->exists()){
            return view('old1.auth.resetPassword', [
                'token' => $token
            ]);
        }else{
           
            return redirect()->route('login');
        }
    }

    public function resetPassword(Request $request)
    {
        $email = User::where('change_PasswordToken', $request->value2)->pluck('email');
        //Update User Password
        $update = User::where('change_PasswordToken', $request->value2)->update([
            'password' => Hash::make($request->value1),
            'change_PasswordToken' => '',
        ]);

        //dd($email);
        app(SiteHelperController::class)->sendSiteEmail(
            $email,
            'Password Reset',
            'Please be advised your password has been successfully reset. ',
            'test'
        );

        return redirect()->route('login');
    }
}
