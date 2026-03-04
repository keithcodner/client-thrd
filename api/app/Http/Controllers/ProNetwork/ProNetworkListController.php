<?php

namespace App\Http\Controllers\ProNetwork;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\PostsController;
use App\Models\Settings\SiteSettings;
use App\Models\ProNetwork\ProNetworkConnections;
use App\Models\ProNetwork\ProNetworkGroup;
use App\Models\ProNetwork\ProNetworkRequests;
use App\Models\ProNetwork\ProNetworkUserProfile;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProNetworkListController extends Controller
{
    public function __construct()
    {
        ////$this->middleware(['auth']);
    }

    public function index()
    {
       // dd(auth()->user()->posts);
    }

    public function searchForPeopleOnProNetworkPage(Request $request)
    {
        $getUser = '';
        if(isset($request->uid)){
            $getUser = $request->uid;
        }else if(!isset($request->uid)){
            $getUser = auth()->user()->id;
        }

        $netGroupData = ProNetworkGroup::where('target_id', $getUser)
            ->where('status', 'active')
            ->where('type', 'person')
            ->first();

        $myConnectionsData = ProNetworkConnections::where(function($q) use ($getUser){
            $q->where('initiator_user_id', $getUser)
            ->where(function($q1){
                $q1->where('status', 'active')
                ->where('type', 'connection')
                ->where('net_request_id', '!=', null);
            });
        })
        ->orWhere(function($q) use ($getUser){
            $q->where('accepter_user_id', $getUser)
            ->where(function($q1){
                $q1->where('status', 'active')
                ->where('type', 'connection')
                ->where('net_request_id', '!=', null);
            });
        })
        ->with(['pronetworkuserprofile_ref_init.pronetworkprofile', 'pronetworkuserprofile_ref_accept.pronetworkprofile'])
        ->get();
        
        return Inertia::render('ZProtoTypes/ProNetwork/ProNetworkList', [
            'netGroupData' => $netGroupData,
            'myConnectionsData' => $myConnectionsData,
            'uid' => $request->uid
            
        ]);
    }

    
}