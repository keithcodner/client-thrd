<?php

namespace App\Http\Controllers\ProNetwork;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\PostsController;
use App\Models\Settings\SiteSettings;
use App\Models\ProNetwork\ProNetworkUserProfile;
use App\Models\ProNetwork\ProNetworkRequests;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProNetworkSearchController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    public function index()
    {
       // dd(auth()->user()->posts);
    }

    public function searchForAllOnSiteRequest(Request $request)
    {
        $searchTerm = $request->q; //query term
        $productType = $request->t; //type: item, service (defined by i or s)
    }

    public function searchForEventsOnSiteRequest(Request $request)
    {
        $searchTerm = $request->q; //query term
        $productType = $request->t; //type: item, service (defined by i or s)
    }


    

    public function searchForPeopleOnProNetworkRequest(Request $request)
    {
        $searchTerm = $request->q; //query term

        $user_ids =  User::where('firstname', 'like', '%' .  $searchTerm . '%')
                        ->orWhere('lastname', 'like', '%' .  $searchTerm . '%')
                        ->pluck('id');
        $avatar_img_path = asset('storage/store_data/users/');
        $proNetworkProfiles = ProNetworkUserProfile::whereIn('user_id', $user_ids)->with(['user'])->get();
        
        return $proNetworkProfiles;

    }

    public function searchForPeopleOnProNetworkPage(Request $request)
    {
        $searchTerm = $request->q; //query
        $page = $request->p; //page
        $searchTerm = preg_replace('~%20| ~', " ", $searchTerm); 

        $user_ids =  User::where('firstname', 'like', '%' .  $searchTerm . '%')
                        ->orWhere('lastname', 'like', '%' .  $searchTerm . '%')
                        ->pluck('id');
        $avatar_img_path = asset('storage/store_data/users/');
        $proNetworkProfiles = ProNetworkUserProfile::whereIn('user_id', $user_ids)->with(['user'])->get();
        
        //$proNetworkRequests = ProNetworkRequests::where('accepter_user_id', auth()->user()->id)->where('status', 'pending')->orWhere('status', 'accepted')->get();

        $proNetworkRequests = ProNetworkRequests::where(function($q){
            $q->where('accepter_user_id', auth()->user()->id)
            ->where(function($q1){
                $q1->where('status', 'pending')
                ->orWhere('status', 'accepted');
            });
        })
        ->orWhere(function($q){
            $q->where('initiator_user_id', auth()->user()->id)
            ->where(function($q1){
                $q1->where('status', 'pending')
                ->orWhere('status', 'accepted');
            });
        })
        ->orderBy('updated_at', 'DESC')
        ->get();

        return Inertia::render('ZProtoTypes/SearchTracking/PeopleSearchPage', [
            'userImagePath' => asset('storage/store_data/users/'),
            'proNetworkProfiles' => $proNetworkProfiles,
            'proNetworkRequests' => $proNetworkRequests
        ]);

    }

    

    
}