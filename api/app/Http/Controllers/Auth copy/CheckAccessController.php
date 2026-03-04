<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SiteSettings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class CheckAccessController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['guest']);
    }

    public function checkAccess(Request $request){
        // Password from CheckAccess middleware
        $correctPassword = "!Gig@Login#99";
        
        if ($request->value1 == 'valid') {
            // Check if the provided password matches
            if ($request->value2 === $correctPassword) {
                // Set session to bypass protection
                Session::put('dataBase', $correctPassword);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Access granted'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid access code'
                ], 401);
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid request format'
        ], 400);
    }
    

    public function store(Request $request)
    {
        
    }

    
}
