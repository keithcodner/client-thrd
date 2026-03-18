<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;
use App\Models\Circles\Circle;
use App\Models\Circles\CircleDetail;
use App\Models\Circles\CircleIdeaBoard;
use App\Models\Circles\CircleMemberTracker;

use App\Models\Conversation\Conversation;
use App\Models\Conversation\ConversationChat;

use \App\Http\Enums\TypesAndStatus\Circle\Circle as CircleEnum;
use \App\Http\Enums\TypesAndStatus\Conversation\Conversation as ConversationEnum;
use \App\Http\Enums\TypesAndStatus\Core\Active as ActiveEnum;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ChatController extends Controller
{
    public function __construct()
    {

    }

    public function createCircle(Request $request)
    {
        try {
            $user = Auth::user();

            // Validate request data
            $validated = $request->validate([
                'name' => 'required|string|max:500',
                'description' => 'nullable|string|max:5000',
                'style_code' => 'nullable|string|in:' . implode(',', CircleEnum::getStyleCodes()),
                'privacy_state' => 'nullable|string|max:50',
                'type' => 'nullable|string|in:' . implode(',', CircleEnum::getTypes()),
                'isPrivate' => 'nullable|boolean',
            ]);

            $circleType = $request->boolean('isPrivate') ? CircleEnum::TYPE_PRIVATE_CIRCLE : CircleEnum::TYPE_COMMUNITY_HUB;

            // Step 1: Create circle
            $circle = Circle::create([
                'user_owner_id' => $user->id,
                'name' => $validated['name'],
                'type' => $circleType,
                'status' => ActiveEnum::STATUS_ACTIVE,
            ]);

            // Step 2: Create circle idea board
            $ideaBoard = CircleIdeaBoard::create([
                'circle_id' => $circle->id,
                'details' => null,
                'type' => 'default',
                'status' => ActiveEnum::STATUS_ACTIVE,
            ]);

            // Step 3: Create circle details
            CircleDetail::create([
                'circle_id' => $circle->id,
                'circle_idea_board_id' => $ideaBoard->id,
                'description' => $validated['description'] ?? null,
                'style_code' => $validated['style_code'] ?? CircleEnum::STYLE_SAGE,
                'privacy_state' => $validated['privacy_state'] ?? 'public',
                'type' => $circleType,
                'status' => ActiveEnum::STATUS_ACTIVE,
            ]);

            // Step 4: Add owner to circle member tracker
            CircleMemberTracker::create([
                'circle_id' => $circle->id,
                'user_id' => $user->id,
                'type' => CircleEnum::TYPE_OWNER,
                'status' => ActiveEnum::STATUS_ACTIVE,
            ]);

            // Step 5: Create conversation for the circle
            $conversation = Conversation::create([
                'owner_user_id' => $user->id,
                'circle_id' => $circle->id,
                'title' => $circle->name,
                'type' =>  ConversationEnum::TYPE_SECOND_CIRCLE,
                'status' => ActiveEnum::STATUS_ACTIVE,
            ]);

            // Step 6: Add system chat to conversation
            ConversationChat::create([
                'init_user_id' => $user->id,
                'conversation_id' => $conversation->id,
                'content' => 'Welcome to your new circle!',
                'type' => ConversationEnum::TYPE_SECOND_SYSTEM,
            ]);

            Log::info('Circle created successfully', [
                'user_id' => $user->id,
                'circle' => $circle,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Circle created successfully.',
                'circle' => $circle,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating circle: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Failed to create circle. Please try again later.',
            ], 500);
        }
    }
}
