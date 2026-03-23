<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;
use App\Models\Circles\Circle;
use App\Models\Circles\CircleDetail;
use App\Models\Circles\CircleIdeaBoard;
use App\Models\Circles\CircleMemberTracker;

use App\Events\NewChatMessage;
use App\Events\UserTyping;
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

    public function getConversationChats(Request $request)
    {
        try {
            $user = Auth::user();

            // Validate request data
            $validated = $request->validate([
                'conversation_id' => 'required|integer|exists:conversations,id',
                'limit' => 'nullable|integer|min:1|max:100',
                'offset' => 'nullable|integer|min:0', // For pagination
            ]);

            $conversationId = $validated['conversation_id'];
            $limit = $validated['limit'] ?? 30;
            $offset = $validated['offset'] ?? 0;

            // Get the conversation
            $conversation = Conversation::findOrFail($conversationId);

            // Verify user has access to this conversation
            // For circle conversations, check if user is a member
            if ($conversation->circle_id) {
                $isMember = CircleMemberTracker::where('circle_id', $conversation->circle_id)
                    ->where('user_id', $user->id)
                    ->where('status', ActiveEnum::STATUS_ACTIVE)
                    ->exists();

                if (!$isMember) {
                    return response()->json([
                        'message' => 'You do not have access to this conversation.',
                    ], 403);
                }
            }

            // Fetch messages with user info
            // Order by created_at DESC to get most recent first, then skip offset
            $messages = ConversationChat::where('conversation_id', $conversationId)
                ->with(['user:id,name,email']) // Load user relationship
                ->orderBy('created_at', 'desc')
                ->skip($offset)
                ->take($limit)
                ->get()
                ->reverse() // Reverse to get chronological order (oldest first)
                ->values(); // Re-index array

            // Transform messages to include sender info
            $formattedMessages = $messages->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'sender' => $message->user->name ?? 'Unknown',
                    'senderId' => $message->init_user_id,
                    'content' => $message->content,
                    'timestamp' => $message->created_at->format('g:i A'),
                    'createdAt' => $message->created_at->toISOString(),
                    'isSystemMessage' => in_array($message->type, ['system', 'announcement']),
                    'isCurrentUser' => $message->init_user_id === $user->id,
                ];
            });

            // Check if there are more messages
            $hasMore = ConversationChat::where('conversation_id', $conversationId)
                ->orderBy('created_at', 'desc')
                ->skip($offset + $limit)
                ->take(1) // MySQL requires LIMIT with OFFSET
                ->exists();

            Log::info('Messages fetched successfully', [
                'user_id' => $user->id,
                'conversation_id' => $conversationId,
                'message_count' => $formattedMessages->count(),
                'offset' => $offset,
                'hasMore' => $hasMore,
            ]);

            return response()->json([
                'messages' => $formattedMessages,
                'hasMore' => $hasMore,
                'conversation' => [
                    'id' => $conversation->id,
                    'title' => $conversation->title,
                    'type' => $conversation->type,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching messages: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Failed to fetch messages. Please try again later.',
            ], 500);
        }
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

    /**
     * Update typing status for a conversation
     * Broadcasts to other users that the current user is typing or stopped typing
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTypingStatus(Request $request)
    {
        try {
            $user = Auth::user();

            // Validate request data
            $validated = $request->validate([
                'conversation_id' => 'required|integer|exists:conversations,id',
                'is_typing' => 'required|boolean',
            ]);

            // Broadcast typing status to other users in the conversation
            broadcast(new UserTyping(
                $validated['conversation_id'],
                $user,
                $validated['is_typing']
            ))->toOthers();

            return response()->json([
                'message' => 'Typing status updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating typing status: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Failed to update typing status.',
            ], 500);
        }
    }
}
