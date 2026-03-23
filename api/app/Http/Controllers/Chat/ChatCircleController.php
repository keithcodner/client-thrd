<?php

namespace App\Http\Controllers\Chat;

use App\Models\Comment\Comment;
use App\Models\User;
use App\Models\Circles\Circle;
use App\Models\Circles\CircleRequest;
use App\Models\Circles\CircleMemberTracker;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ChatCircleController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
    }

    public function store(Request $request)
    {
        
    }

    /**
     * Search users for circle invites
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUsers(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1'
        ]);

        $query = $request->input('query');
        $currentUserId = $request->user()->id;

        // Search users by firstname, lastname, or username
        // Exclude the current user from results and filter out users without names
        $users = User::where('id', '!=', $currentUserId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('firstname', 'LIKE', "%{$query}%")
                    ->orWhere('lastname', 'LIKE', "%{$query}%")
                    ->orWhere('username', 'LIKE', "%{$query}%");
            })
            ->where(function ($q) {
                $q->whereNotNull('firstname')
                    ->orWhereNotNull('name')
                    ->orWhereNotNull('username');
            })
            ->select('id', 'name', 'firstname', 'lastname', 'username', 'avatar')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    public function sendCircleChatInvite(Request $request)
    {
        $request->validate([
            'circle_id' => 'required|integer|exists:circles,id',
            'invited_user_id' => 'required|integer|exists:users,id',
        ]);

        $circleId = $request->input('circle_id');
        $invitedUserId = $request->input('invited_user_id');
        $currentUserId = $request->user()->id;

        // Check if user is already a member of the circle
        $isAlreadyMember = CircleMemberTracker::where('circle_id', $circleId)
            ->where('user_id', $invitedUserId)
            ->where('status', 'active')
            ->exists();

        if ($isAlreadyMember) {
            return response()->json([
                'success' => false,
                'message' => 'This user is already a member of the circle.'
            ], 400);
        }

        // Check if ANY pending request already exists for this user and circle
        $existingRequest = CircleRequest::where('circle_id', $circleId)
            ->where('requesting_to_join_user_id', $invitedUserId)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'A pending invite already exists for this user.'
            ], 400);
        }

        // Get circle information
        $circle = Circle::find($circleId);
        if (!$circle) {
            return response()->json([
                'success' => false,
                'message' => 'Circle not found.'
            ], 404);
        }

        // Create circle request
        $circleRequest = CircleRequest::create([
            'circle_id' => $circleId,
            'requester_user_id' => $currentUserId,
            'requesting_to_join_user_id' => $invitedUserId,
            'type' => 'circle_request',
            'status' => 'pending',
        ]);

        // Get circle name
        $circleName = $circle->name ?? 'a circle';

        // Create notification for the invited user
        Notification::create([
            'user_id' => $invitedUserId,
            'from_id' => $currentUserId,
            'fk_circle_item_post_id' => $circleId,
            'type' => 'circle_request',
            'title' => "You received an invite from {$circleName}",
            'comment' => "You have received an invite to join the circle \"{$circleName}\". Review the circle and decide if you'd like to join.",
            'status' => 'unread',
            'color_status' => Notification::getRandomColor(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Circle invite sent successfully.',
            'request' => $circleRequest
        ]);
    }

    public function acceptCircleChatInvite(Request $request)
    {
        $request->validate([
            'request_id' => 'required|integer|exists:circles_requests,id',
        ]);

        $requestId = $request->input('request_id');
        $currentUserId = $request->user()->id;

        // Get the circle request
        $circleRequest = CircleRequest::find($requestId);

        if (!$circleRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Circle request not found.'
            ], 404);
        }

        // Verify the current user is the one being invited
        if ($circleRequest->requesting_to_join_user_id != $currentUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        // Check if already accepted
        if ($circleRequest->status === 'accepted') {
            return response()->json([
                'success' => false,
                'message' => 'This invite has already been accepted.'
            ], 400);
        }

        // Update the circle request status
        $circleRequest->update([
            'status' => 'accepted'
        ]);

        // Add user to circle members
        CircleMemberTracker::create([
            'circle_id' => $circleRequest->circle_id,
            'user_id' => $currentUserId,
            'type' => 'member',
            'status' => 'active',
        ]);

        // Get circle information
        $circle = Circle::find($circleRequest->circle_id);
        $circleName = $circle->name ?? 'a circle';

        // Create notification for the requester
        Notification::create([
            'user_id' => $circleRequest->requester_user_id,
            'from_id' => $currentUserId,
            'fk_circle_item_post_id' => $circleRequest->circle_id,
            'type' => 'circle_request',
            'title' => 'Your circle invite has been accepted',
            'comment' => "Your invite to join \"{$circleName}\" has been accepted. The user is now a member of the circle.",
            'status' => 'unread',
            'color_status' => Notification::getRandomColor(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Circle invite accepted successfully.',
        ]);
    }

    public function denyCircleChatInvite(Request $request)
    {
        $request->validate([
            'request_id' => 'required|integer|exists:circles_requests,id',
        ]);

        $requestId = $request->input('request_id');
        $currentUserId = $request->user()->id;

        // Get the circle request
        $circleRequest = CircleRequest::find($requestId);

        if (!$circleRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Circle request not found.'
            ], 404);
        }

        // Verify the current user is the one being invited
        if ($circleRequest->requesting_to_join_user_id != $currentUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        // Check if already declined
        if ($circleRequest->status === 'declined') {
            return response()->json([
                'success' => false,
                'message' => 'This invite has already been declined.'
            ], 400);
        }

        // Update the circle request status
        $circleRequest->update([
            'status' => 'declined'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Circle invite declined successfully.',
        ]);
    }

    public function getPendingCircleInvites(Request $request)
    {
        $request->validate([
            'circle_id' => 'required|integer|exists:circles,id',
        ]);

        $circleId = $request->input('circle_id');
        $currentUserId = $request->user()->id;

        // Verify user is a member of the circle
        $isMember = CircleMemberTracker::where('circle_id', $circleId)
            ->where('user_id', $currentUserId)
            ->where('status', 'active')
            ->exists();

        if (!$isMember) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this circle.'
            ], 403);
        }

        // Get all pending invites for this circle
        $pendingInvites = CircleRequest::where('circle_id', $circleId)
            ->where('status', 'pending')
            ->where('type', 'circle_request')
            ->pluck('requesting_to_join_user_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'pending_user_ids' => $pendingInvites,
        ]);
    }

}
