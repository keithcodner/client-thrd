<?php

namespace App\Http\Controllers\ProNetwork;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\NotificationsController;
use App\Http\Controllers\Core\PostsController;
use App\Http\Controllers\Core\SiteHelperController;
use App\Mail\SiteMailServer;
use App\Models\Settings\SiteSettings;
use App\Models\ProNetwork\ProNetworkConnections;
use App\Models\ProNetwork\ProNetworkGroup;
use App\Models\ProNetwork\ProNetworkRequests;
use App\Models\ProNetwork\ProNetworkUserProfile;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ProNetworkRequestsController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    public function index()
    {
       // dd(auth()->user()->posts);
    }

    public function incomingProNetworkRequestsPage(Request $request)
    {

        $proNetworkRequests = ProNetworkRequests::where('accepter_user_id', auth()->user()->id)->where('status', 'pending')->with(['user_accept', 'user_initiate'])->get();

        return Inertia::render('ZProtoTypes/ProNetwork/ProNetworkIncomingRequests', [
            'proNetworkRequests' => $proNetworkRequests,
            'id' => auth()->user()->id
        ]);
    }

    /*
                        ********createProNetworkConnectionRequestResponse********
        - Reuqest Parameters: pronetwork request id should suffice
        - find the pronetwork group where target_id is the user id of the accepter (confirm it exists first)
        - find the active pronetwork request data (temporarily store...we need info from it)
        - create a new pronetwork connection record with pronetwork groupid as net_group_id, initiator_user and accepter_user from (and other data)...also added last minute (net_request_id with pronetwork request table id) to track which request create which connection. 
        - update the request status' (status = connected, isAccepted = true)
    */

    public function createProNetworkConnectionRequestResponse(Request $request)
    {
        //- parameters: value1->pronetwork_request_id, value2->deny or acccept option
        if($request->value2 === 'accept'){
            $this->incomingProNetworkRequestsAcceptRequest($request->value1);
        }else if($request->value2 === 'deny'){
            $this->incomingProNetworkRequestsDenyRequest($request->value1);
        }
    }

    public function incomingProNetworkRequestsAcceptRequest($key)
    {
        //Validate user accepting the request; the request should exist, they should have their profile turned on, request should be pending, group status should be active, user should be authenticated, group type should be person
        if(ProNetworkRequests::where('id', $key)
                ->where('accepter_user_id', auth()->user()->id)
                ->where('status', 'pending')->exists() 
            && ProNetworkGroup::where('target_id', auth()->user()->id)
                ->where('status', 'active')
                ->where('type', 'person')->exists())
        {
            //Get request data
            $myCurrentAcceptingRequest = ProNetworkRequests::where('id', $key)->where('accepter_user_id', auth()->user()->id)->where('status', 'pending')->first();

            //dd($myCurrentAcceptingRequest );

            $netGroupData = ProNetworkGroup::where('target_id', auth()->user()->id)->where('status', 'active')->where('type', 'person')->first();

            //Create network connection from request
            $create = ProNetworkConnections::create([
                'net_group_id' => $netGroupData->id,
                'net_request_id' => $key,
                'an_id' => app(SiteHelperController::class)->createAlphaNumericId(),
                'initiator_user_id' => $myCurrentAcceptingRequest->initiator_user_id,
                'accepter_user_id' => $myCurrentAcceptingRequest->accepter_user_id,
                'type' => 'connection', //- person, group
                'isConnected' => 'true', //- pending, accepted, denied  
                'status' => 'active', //- true
            ]);

            //Update request record
            $update = ProNetworkRequests::where('id', $key)->update([
                'status' => 'accepted', //- pending, accepted, denied
                'isAccepted' => 'true', //- true
            ]);

            //TODO: Need to update connection count
            $this->updateProNetworkConnectionCount($myCurrentAcceptingRequest->initiator_user_id, $myCurrentAcceptingRequest->accepter_user_id);

            //Send email to initiator
            //- Mail::to($request->email)->send(new SiteMailServer(''));

            //Send notification of acceptance to the initiator
            app(NotificationsController::class)->generateSiteNotification(
                'xUser has accepted your request',
                'Start interacting with your new connection now!',
                'pronetwork_request_accept',
                $myCurrentAcceptingRequest->initiator_user_id, //- user id
                0, //- from id
                "off",
            );

        }else{
            return 'Could not find pronetwork request';
        }
    }

    public function incomingProNetworkRequestsDenyRequest($key)
    {
        if(ProNetworkRequests::where('id', $key)
                ->where('accepter_user_id', auth()->user()->id)
                ->where('status', 'pending')->exists() 
            && ProNetworkGroup::where('target_id', auth()->user()->id)
                ->where('status', 'active')
                ->where('type', 'person')->exists())
        {
            //Update request record
            $update = ProNetworkRequests::where('id', $key)->update([
                'status' => 'denied', //- pending, accepted, denied
                'isAccepted' => 'false', //- true
            ]);

            //TODO: Need to update connection count

            //Refrain from sending email or notificaitons of rejection

        }else{
            return 'Could not find pronetwork request';
        }
    }

    public function incomingProNetworkRequestsAcceptAllRequest(Request $request)
    {
        //TODO: Need to update connection count
    }

    public function incomingProNetworkRequestsDenyAllRequest(Request $request)
    {
        //TODO: Need to update connection count
    }

    public function connectedProNetworkRequestsDisconnectedRequest(Request $request)
    {
        //TODO: Need to update connection count
    }

    public function updateProNetworkConnectionCount($accepter_user_id, $target_id)
    {
        $myConnectionsDataCount_accepter = ProNetworkConnections::where(function($q) use ($accepter_user_id){
            $q->where('initiator_user_id', $accepter_user_id)
            ->where(function($q1){
                $q1->where('status', 'active')
                ->where('type', 'connection')
                ->where('net_request_id', '!=', null);
            });
        })
        ->orWhere(function($q)  use ($accepter_user_id){
            $q->where('accepter_user_id', $accepter_user_id)
            ->where(function($q1){
                $q1->where('status', 'active')
                ->where('type', 'connection')
                ->where('net_request_id', '!=', null);
            });
        })->count();

        $myConnectionsDataCount_target = ProNetworkConnections::where(function($q) use ($target_id){
            $q->where('initiator_user_id', $target_id)
            ->where(function($q1){
                $q1->where('status', 'active')
                ->where('type', 'connection')
                ->where('net_request_id', '!=', null);
            });
        })
        ->orWhere(function($q)  use ($target_id){
            $q->where('accepter_user_id', $target_id)
            ->where(function($q1){
                $q1->where('status', 'active')
                ->where('type', 'connection')
                ->where('net_request_id', '!=', null);
            });
        })->count();

        $update = ProNetworkUserProfile::where('user_id', $accepter_user_id)->update([
            'connections_count' => $myConnectionsDataCount_accepter, //- pending, accepted, denied
        ]);

        $update = ProNetworkUserProfile::where('user_id', $target_id)->update([
            'connections_count' => $myConnectionsDataCount_target, //- pending, accepted, denied
        ]);
    }

    /*
                        ********createProNetworkConnectionRequest********
        - Request values: value1->initiator_id, value2->acceptor_id, 
        -  we should determine if they are trying to connect to themselves first if this is what the person is attempting - DONE
        -  this should also be caught on the front end (where the button is disabled)
        -  determine if the person they are requesting is already connected; this should be handled by front end, but we need back end valuation - DONE
        -  determine if there is already an active network request ( front end should handle but you know the drift) - DONE
        -  a request record should be created, and the recipient notified ( need a page for network requests view)
        -  the recipient sees the requests and either accepts or denies
        -  when they denied the request, update the request table; update initiator
        -  when they accept the request, update the requests table; add a new record to connection table; update the initiator.
        - when displaying accout connections store the actual amount as a value and update as each connection is accepted OR *** disconnected*** AND NOT denied connection
        -  update ran
    */
    
    public function createProNetworkConnectionRequest(Request $request)
    {
        if (ProNetworkConnections::where('initiator_user_id', $request->value1 )->where('accepter_user_id', $request->value2)->exists() && ProNetworkRequests::where('initiator_user_id', $request->value1 )->where('accepter_user_id', $request->value2)->where('status', 'accepted')->where('isAccepted', 'true')->exists()){
            return ' You are already connected to this user.'; //- done
        }else if($request->value1 === $request->value2){
            return 'Sorry, you cannot connect to your own ProNetwork profile.'; //- done
        }else if(ProNetworkRequests::where('initiator_user_id', $request->value1 )->where('accepter_user_id', $request->value2)->where('status', 'pending')->exists()){
            return 'You already have a ProNetwork request pending with this user'; //- done
        }else if(ProNetworkRequests::where('initiator_user_id', $request->value1 )->where('accepter_user_id', $request->value2)->where('status', 'pending')->exists() || ProNetworkRequests::where('initiator_user_id', $request->value2 )->where('accepter_user_id', $request->value1)->where('status', 'pending')->exists()){
            return 'You did not inititate the request, but you already have a pending request with this user. Check your incoming ProNetwork Requests'; //- done
        }else{
           //- Create ProNetwork connection to the requested profile
            $pronetwork_connection = ProNetworkRequests::create([
                'initiator_user_id' => $request->value1,
                'accepter_user_id' => $request->value2,
                'type' => 'person', //- person, group
                'status' => 'pending', //- pending, accepted, denied
                'isAccepted' => 'false', //- true
            ]);

            //- Mail::to($request->email)->send(new SiteMailServer(''));

            app(NotificationsController::class)->generateSiteNotification(
                'Someone would like to connect with you',
                'xUser Would like to connect with you, do you accept?',
                'pronetwork_request',
                $request->value2, //- user id
                0, //- from id
                "off",
            );
        }



    }
}
