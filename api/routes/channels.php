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
if (!function_exists('authorizeConversationAccess')) {
    function authorizeConversationAccess($user, $conversationId)
    {
        \Log::info('authorizeConversationAccess called', [
            'user_id' => $user->id,
            'conversation_id' => $conversationId,
        ]);
        
        $conversation = Conversation::find($conversationId);
        
        if (!$conversation) {
            \Log::warning('Conversation not found', [
                'conversation_id' => $conversationId,
                'user_id' => $user->id,
            ]);
            return false;
        }
        
        \Log::info('Conversation found', [
            'conversation_id' => $conversationId,
            'type' => $conversation->type,
            'circle_id' => $conversation->circle_id,
            'owner_user_id' => $conversation->owner_user_id,
        ]);
        
        // For 1-to-1 conversations (couple)
        if ($conversation->type === 'couple') {
            $authorized = $conversation->owner_user_id === $user->id || 
                   $conversation->to_id === $user->id;
            \Log::info('Couple conversation authorization', [
                'authorized' => $authorized,
                'user_id' => $user->id,
            ]);
            return $authorized;
        }
        
        // For circle conversations (group)
        if ($conversation->circle_id) {
            \Log::info('Checking circle membership', [
                'circle_id' => $conversation->circle_id,
                'user_id' => $user->id,
            ]);
            
            // Get all members for this circle to debug
            $allMembers = CircleMemberTracker::where('circle_id', $conversation->circle_id)
                ->get(['user_id', 'status', 'type']);
            
            \Log::info('All circle members', [
                'circle_id' => $conversation->circle_id,
                'members' => $allMembers->toArray(),
            ]);
            
            $isMember = CircleMemberTracker::where('circle_id', $conversation->circle_id)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();
            
            \Log::info('Circle conversation authorization result', [
                'circle_id' => $conversation->circle_id,
                'user_id' => $user->id,
                'is_member' => $isMember,
                'check_query' => [
                    'circle_id' => $conversation->circle_id,
                    'user_id' => $user->id,
                    'status' => 'active',
                ],
            ]);
            
            return $isMember;
        }
        
        \Log::warning('Conversation has no circle_id and is not couple type', [
            'conversation_id' => $conversationId,
            'type' => $conversation->type,
        ]);
        
        return false;
    }
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
    \Log::info('========== PRESENCE CHANNEL AUTH START ==========');
    \Log::info('Presence Channel Authorization Attempt', [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'user_email' => $user->email,
        'conversation_id' => $conversationId,
        'conversation_id_type' => gettype($conversationId),
    ]);
    
    $authorized = authorizeConversationAccess($user, $conversationId);
    
    if ($authorized) {
        $presenceData = [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ?? null,
            'last_active' => $user->updated_at->toISOString(),
        ];
        
        \Log::info('✅ Presence Channel AUTHORIZED', [
            'user_id' => $user->id,
            'conversation_id' => $conversationId,
            'presence_data' => $presenceData,
        ]);
        \Log::info('========== PRESENCE CHANNEL AUTH END (SUCCESS) ==========');
        
        return $presenceData;
    }
    
    \Log::error('❌ Presence Channel ACCESS DENIED', [
        'user_id' => $user->id,
        'user_email' => $user->email,
        'conversation_id' => $conversationId,
        'reason' => 'authorizeConversationAccess returned false',
    ]);
    \Log::info('========== PRESENCE CHANNEL AUTH END (DENIED) ==========');
    
    return false;
});
