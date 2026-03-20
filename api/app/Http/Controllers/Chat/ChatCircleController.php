<?php

namespace App\Http\Controllers\Chat;

use App\Models\Comment\Comment;
use App\Models\User;
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
}
