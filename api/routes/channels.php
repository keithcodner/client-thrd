<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation\Conversation;
use App\Models\Circles\CircleMemberTracker;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Helper function to authorize conversation access
function authorizeConversationAccess($user, $conversationId)
{
    $conversation = Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }
    
    // For 1-to-1 conversations (couple)
    if ($conversation->type === 'couple') {
        return $conversation->owner_user_id === $user->id || 
               $conversation->to_id === $user->id;
    }
    
    // For circle conversations (group)
    if ($conversation->circle_id) {
        return CircleMemberTracker::where('circle_id', $conversation->circle_id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }
    
    return false;
}

// Authenticate private chat channels
Broadcast::channel('sitePrivateChat.{conversationId}', function ($user, $conversationId) {
    \Log::info('WebSocket Authorization Attempt', [
        'user_id' => $user->id,
        'conversation_id' => $conversationId,
    ]);
    
    $authorized = authorizeConversationAccess($user, $conversationId);
    
    \Log::info('WebSocket Authorization Result', [
        'user_id' => $user->id,
        'conversation_id' => $conversationId,
        'authorized' => $authorized,
    ]);
    
    return $authorized;
});

// Typing indicator channel
Broadcast::channel('typing.{conversationId}', function ($user, $conversationId) {
    return authorizeConversationAccess($user, $conversationId);
});

// Presence channel for online status
Broadcast::channel('presence-conversation.{conversationId}', function ($user, $conversationId) {
    if (authorizeConversationAccess($user, $conversationId)) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ?? null,
            'last_active' => $user->updated_at->toISOString(),
        ];
    }
    return false;
});
