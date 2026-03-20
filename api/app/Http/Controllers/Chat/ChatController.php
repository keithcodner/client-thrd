<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;
use App\Models\Circles\Circle;
use App\Models\Circles\CircleDetail;
use App\Models\Circles\CircleIdeaBoard;
use App\Models\Circles\CircleMemberTracker;

use App\Events\NewChatMessage;
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

            // Determine circle type and privacy state based on isPrivate flag
            $circleType = $request->boolean('isPrivate') ? CircleEnum::TYPE_PRIVATE_CIRCLE : CircleEnum::TYPE_COMMUNITY_HUB;

            $circlePrivacyState = $request->boolean('isPrivate') ? CircleEnum::PRIVACY_PRIVATE : CircleEnum::PRIVACY_PUBLIC;

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
                'privacy_state' => $circlePrivacyState,
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

    public function getUserCircleData(Request $request)
    {
        $user = Auth::user();

        Log::info('getUserCircleData called', [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);

        // Get all circles where the user is an active member
        $circles = Circle::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('status', ActiveEnum::STATUS_ACTIVE);
        })
        ->with(['details', 'ideaBoard', 'members' => function ($query) {
            $query->where('status', ActiveEnum::STATUS_ACTIVE);
        }])
        ->get();

        Log::info('getUserCircleData result', [
            'user_id' => $user->id,
            'circles_count' => $circles->count(),
            'circles' => $circles->pluck('id', 'name'),
        ]);

        return response()->json([
            'circles' => $circles,
        ], 200);
    }

    public function postChat(Request $request)
    {
        try {
            $user = Auth::user();

            // Validate request data
            $validated = $request->validate([
                'conversation_id' => 'required|integer|exists:conversations,id',
                'content' => 'required|string|max:5000',
                'type' => 'nullable|string|in:chat,announcement,system',
                'end_user_id' => 'nullable|integer|exists:users,id',
            ]);

            // Get the conversation to determine if it's a circle or 1-to-1
            $conversation = Conversation::findOrFail($validated['conversation_id']);

            // Prepare chat data
            $chatData = [
                'init_user_id' => $user->id,
                'conversation_id' => $validated['conversation_id'],
                'content' => $validated['content'],
                'type' => $validated['type'] ?? 'chat',
                'seen_by_other_user' => 'false',
                'seen_by_received_user' => 'false',
            ];

            // For 1-to-1 conversations (TYPE_COUPLE), set end_user_id
            if ($conversation->type === ConversationEnum::TYPE_COUPLE) {
                // Validate that end_user_id is provided for 1-to-1 conversations
                if (!isset($validated['end_user_id'])) {
                    return response()->json([
                        'message' => 'end_user_id is required for 1-to-1 conversations.',
                    ], 400);
                }

                // Prevent users from sending messages to themselves
                if ($user->id == $validated['end_user_id']) {
                    return response()->json([
                        'message' => 'You cannot send messages to yourself.',
                    ], 400);
                }

                $chatData['end_user_id'] = $validated['end_user_id'];
            }
            // For circle conversations (TYPE_GROUP), end_user_id is not used
            // Messages are read first-come-first-serve

            // Create the chat message
            $newChatMessage = ConversationChat::create($chatData);

            // Broadcast the new message to other users
            broadcast(new NewChatMessage($newChatMessage))->toOthers();

            Log::info('Chat message sent successfully', [
                'user_id' => $user->id,
                'conversation_id' => $validated['conversation_id'],
                'chat_id' => $newChatMessage->id,
            ]);

            return response()->json([
                'message' => 'Message sent successfully.',
                'chat' => $newChatMessage,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error sending chat message: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Failed to send message. Please try again later.',
            ], 500);
        }
    }
}
