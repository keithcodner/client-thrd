<?php

namespace App\Http\Controllers\ProNetwork;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\PostsController;
use App\Models\Settings\SiteSettings;
use App\Models\Local\SchoolsCanada;
use App\Models\Local\SchoolsUS;
use App\Models\ProNetwork\ProNetworkConnections;
use App\Models\ProNetwork\ProNetworkGroup;
use App\Models\ProNetwork\ProNetworkRequests;
use App\Models\ProNetwork\ProNetworkUserProfile;
use App\Models\Posts;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProNetworkHelpers extends Controller
{
    public function __construct()
    {
        ////$this->middleware(['auth']);
    }

    public function index()
    {
       // dd(auth()->user()->posts);
    }

    public function getCanadianSchoolsByProvince(Request $request)
    {
        $pre_text = $request->value1; //sample data: US_AL
        $pre_text = explode("_", $pre_text);

        $data = SchoolsCanada::where(function($q) use ($pre_text){
            $q->where('Prov_Terr', $pre_text[1])
            ->where(function($q1){
                $q1->where('Facility_Type', 'like' , '%univers%');
            });
        })
        ->orWhere(function($q) use ($pre_text){
            $q->where('Prov_Terr', $pre_text[1])
            ->where(function($q1){
                $q1->where('Facility_Type', 'like' , '%college%');
            });
        })
        ->orWhere(function($q) use ($pre_text){
            $q->where('Prov_Terr', $pre_text[1])
            ->where(function($q1){
                $q1->where('Facility_Type', 'like' , '%Private Institution%');
            });
        })
        ->get();
        return $data;
    }

    public function getAmericanSchoolsByState(Request $request)
    {
        $pre_text = $request->value1;
        $pre_text = explode("_", $pre_text);
        //dd($pre_text);
        $data = SchoolsUS::where('STATE', $pre_text[1])->get();
        
        return $data;
    }

    
  
}