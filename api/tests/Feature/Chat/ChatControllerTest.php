<?php

use App\Models\User;
use App\Models\Circles\Circle;
use App\Models\Circles\CircleDetail;
use App\Models\Circles\CircleIdeaBoard;
use App\Models\Circles\CircleMemberTracker;
use App\Models\Conversation\Conversation;
use App\Models\Conversation\ConversationChat;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

// ---------------------------------------------------------------------------
// POST /api/create-circle
// ---------------------------------------------------------------------------

describe('createCircle', function () {

    it('creates a circle with all related records for authenticated user', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/create-circle', [
            'name'       => 'My Test Circle',
            'isPrivate'  => false,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'circle' => ['id', 'name']])
            ->assertJsonPath('circle.name', 'My Test Circle');

        $this->assertDatabaseHas('circles', [
            'user_owner_id' => $user->id,
            'name'          => 'My Test Circle',
            'type'          => 'community_hub',
        ]);

        $circle = Circle::where('name', 'My Test Circle')->first();

        $this->assertDatabaseHas('circles_member_tracker', [
            'circle_id' => $circle->id,
            'user_id'   => $user->id,
            'type'      => 'owner',
        ]);

        $this->assertDatabaseHas('conversations', [
            'circle_id' => $circle->id,
            'title'     => 'My Test Circle',
        ]);
    });

    it('creates a private circle when isPrivate is true', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/create-circle', [
            'name'      => 'Private Circle',
            'isPrivate' => true,
        ])->assertStatus(201);

        $this->assertDatabaseHas('circles', [
            'user_owner_id' => $user->id,
            'type'          => 'private_circle',
        ]);

        $circle = Circle::where('name', 'Private Circle')->first();

        $this->assertDatabaseHas('circle_details', [
            'circle_id'     => $circle->id,
            'privacy_state' => 'private',
        ]);
    });

    it('returns 422 when name is missing', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/create-circle', [])->assertStatus(422);
    });

    it('returns 422 when name exceeds 500 characters', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/create-circle', [
            'name' => str_repeat('a', 501),
        ])->assertStatus(422);
    });

    it('returns 422 when style_code is invalid', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/create-circle', [
            'name'       => 'Circle',
            'style_code' => 'invalid_style',
        ])->assertStatus(422);
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/create-circle', [
            'name' => 'Circle',
        ])->assertStatus(401);
    });
});

// ---------------------------------------------------------------------------
// POST /api/user-circles
// ---------------------------------------------------------------------------

describe('getUserCircleData', function () {

    it('returns circles for the authenticated user', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $circle = Circle::create([
            'user_owner_id' => $user->id,
            'name'          => 'User Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $user->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        Conversation::create([
            'owner_user_id' => $user->id,
            'circle_id'     => $circle->id,
            'title'         => 'User Circle',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        $response = $this->postJson('/api/user-circles');

        $response->assertStatus(200)
            ->assertJsonStructure(['circles'])
            ->assertJsonCount(1, 'circles');
    });

    it('returns empty circles when user has no memberships', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/user-circles')
            ->assertStatus(200)
            ->assertJsonCount(0, 'circles');
    });

    it('includes conversation_id in circle data', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $circle = Circle::create([
            'user_owner_id' => $user->id,
            'name'          => 'Circle With Convo',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $user->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'circle_id'     => $circle->id,
            'title'         => 'Circle With Convo',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        $response = $this->postJson('/api/user-circles');

        $response->assertStatus(200);
        expect($response->json('circles.0.conversation_id'))->toBe($conversation->id);
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/user-circles')->assertStatus(401);
    });
});

// ---------------------------------------------------------------------------
// POST /api/chat (getConversationChats)
// ---------------------------------------------------------------------------

describe('getConversationChats', function () {

    it('returns messages for a conversation the user has access to', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $circle = Circle::create([
            'user_owner_id' => $user->id,
            'name'          => 'Chat Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $user->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'circle_id'     => $circle->id,
            'title'         => 'Chat Circle',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        ConversationChat::create([
            'init_user_id'   => $user->id,
            'conversation_id' => $conversation->id,
            'content'        => 'Hello!',
            'type'           => 'chat',
        ]);

        $response = $this->postJson('/api/chat', [
            'conversation_id' => $conversation->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'messages',
                'hasMore',
                'conversation' => ['id', 'title', 'type'],
            ])
            ->assertJsonCount(1, 'messages');

        expect($response->json('messages.0.content'))->toBe('Hello!');
    });

    it('returns 403 when user is not a circle member', function () {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        Sanctum::actingAs($outsider);

        $circle = Circle::create([
            'user_owner_id' => $owner->id,
            'name'          => 'Private',
            'type'          => 'private_circle',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $owner->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $conversation = Conversation::create([
            'owner_user_id' => $owner->id,
            'circle_id'     => $circle->id,
            'title'         => 'Private',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        $this->postJson('/api/chat', [
            'conversation_id' => $conversation->id,
        ])->assertStatus(403);
    });

    it('returns 422 when conversation_id is missing', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/chat', [])->assertStatus(422);
    });

    it('returns 422 when conversation_id does not exist', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/chat', [
            'conversation_id' => 999999,
        ])->assertStatus(422);
    });

    it('respects limit and offset parameters', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $circle = Circle::create([
            'user_owner_id' => $user->id,
            'name'          => 'Pagination Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $user->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'circle_id'     => $circle->id,
            'title'         => 'Pagination Circle',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        foreach (range(1, 5) as $i) {
            ConversationChat::create([
                'init_user_id'    => $user->id,
                'conversation_id' => $conversation->id,
                'content'         => "Message {$i}",
                'type'            => 'chat',
            ]);
        }

        $response = $this->postJson('/api/chat', [
            'conversation_id' => $conversation->id,
            'limit'           => 2,
        ]);

        $response->assertStatus(200);
        expect($response->json('messages'))->toHaveCount(2);
        expect($response->json('hasMore'))->toBeTrue();
    });

    it('marks isCurrentUser correctly for own messages', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $circle = Circle::create([
            'user_owner_id' => $user->id,
            'name'          => 'My Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $user->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'circle_id'     => $circle->id,
            'title'         => 'My Circle',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        ConversationChat::create([
            'init_user_id'    => $user->id,
            'conversation_id' => $conversation->id,
            'content'         => 'My message',
            'type'            => 'chat',
        ]);

        $response = $this->postJson('/api/chat', [
            'conversation_id' => $conversation->id,
        ]);

        $response->assertStatus(200);
        expect($response->json('messages.0.isCurrentUser'))->toBeTrue();
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/chat', [
            'conversation_id' => 1,
        ])->assertStatus(401);
    });
});

// ---------------------------------------------------------------------------
// POST /api/post-chat
// ---------------------------------------------------------------------------

describe('postChat', function () {

    it('creates a chat message and broadcasts it', function () {
        Event::fake();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'circle_id'     => null,
            'title'         => 'Test Convo',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        $response = $this->postJson('/api/post-chat', [
            'conversation_id' => $conversation->id,
            'content'         => 'Hello world',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'chat'])
            ->assertJsonPath('message', 'Message sent successfully.');

        $this->assertDatabaseHas('conversation_chats', [
            'conversation_id' => $conversation->id,
            'init_user_id'    => $user->id,
            'content'         => 'Hello world',
        ]);
    });

    it('returns 400 when sending to couple conversation without end_user_id', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $otherUser = User::factory()->create();

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'title'         => '1-to-1',
            'type'          => 'couple',
            'status'        => 'active',
        ]);

        $this->postJson('/api/post-chat', [
            'conversation_id' => $conversation->id,
            'content'         => 'Hi',
        ])->assertStatus(400)
          ->assertJsonPath('message', 'end_user_id is required for 1-to-1 conversations.');
    });

    it('returns 400 when user tries to send a message to themselves', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'title'         => '1-to-1',
            'type'          => 'couple',
            'status'        => 'active',
        ]);

        $this->postJson('/api/post-chat', [
            'conversation_id' => $conversation->id,
            'content'         => 'Hi',
            'end_user_id'     => $user->id,
        ])->assertStatus(400)
          ->assertJsonPath('message', 'You cannot send messages to yourself.');
    });

    it('creates a couple conversation message with end_user_id', function () {
        Event::fake();
        $user = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($user);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'title'         => '1-to-1',
            'type'          => 'couple',
            'status'        => 'active',
        ]);

        $response = $this->postJson('/api/post-chat', [
            'conversation_id' => $conversation->id,
            'content'         => 'Hey there',
            'end_user_id'     => $other->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('conversation_chats', [
            'conversation_id' => $conversation->id,
            'end_user_id'     => $other->id,
        ]);
    });

    it('returns 422 when content is missing', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'title'         => 'Test',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        $this->postJson('/api/post-chat', [
            'conversation_id' => $conversation->id,
        ])->assertStatus(422);
    });

    it('returns 422 when conversation_id does not exist', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/post-chat', [
            'conversation_id' => 999999,
            'content'         => 'Hello',
        ])->assertStatus(422);
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/post-chat', [
            'conversation_id' => 1,
            'content'         => 'Hello',
        ])->assertStatus(401);
    });
});

// ---------------------------------------------------------------------------
// POST /api/typing-status
// ---------------------------------------------------------------------------

describe('updateTypingStatus', function () {

    it('broadcasts typing status successfully', function () {
        Event::fake();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'title'         => 'Typing Convo',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        $this->postJson('/api/typing-status', [
            'conversation_id' => $conversation->id,
            'is_typing'       => true,
        ])->assertStatus(200)
          ->assertJsonPath('message', 'Typing status updated successfully.');
    });

    it('returns 422 when conversation_id is missing', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/typing-status', [
            'is_typing' => true,
        ])->assertStatus(422);
    });

    it('returns 422 when is_typing is missing', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'title'         => 'Test',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        $this->postJson('/api/typing-status', [
            'conversation_id' => $conversation->id,
        ])->assertStatus(422);
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/typing-status', [
            'conversation_id' => 1,
            'is_typing'       => true,
        ])->assertStatus(401);
    });
});

// ---------------------------------------------------------------------------
// GET /api/unread-message-counts
// ---------------------------------------------------------------------------

describe('getUnreadMessageCounts', function () {

    it('returns zero total unread when no messages exist', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/unread-message-counts');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('total_unread', 0)
            ->assertJsonPath('unread_by_conversation', []);
    });

    it('counts unread messages from other users', function () {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($user);

        $circle = Circle::create([
            'user_owner_id' => $user->id,
            'name'          => 'Unread Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $user->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'circle_id'     => $circle->id,
            'title'         => 'Unread Circle',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        ConversationChat::create([
            'init_user_id'        => $other->id,
            'conversation_id'     => $conversation->id,
            'content'             => 'Unread message',
            'type'                => 'chat',
            'seen_by_other_user'  => 'false',
        ]);

        $response = $this->getJson('/api/unread-message-counts');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('total_unread', 1);
    });

    it('does not count own messages as unread', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $circle = Circle::create([
            'user_owner_id' => $user->id,
            'name'          => 'Own Message Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $user->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'circle_id'     => $circle->id,
            'title'         => 'Own Message Circle',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        ConversationChat::create([
            'init_user_id'        => $user->id,
            'conversation_id'     => $conversation->id,
            'content'             => 'My own message',
            'type'                => 'chat',
            'seen_by_other_user'  => 'false',
        ]);

        $this->getJson('/api/unread-message-counts')
            ->assertStatus(200)
            ->assertJsonPath('total_unread', 0);
    });

    it('returns 401 for unauthenticated request', function () {
        $this->getJson('/api/unread-message-counts')->assertStatus(401);
    });
});

// ---------------------------------------------------------------------------
// POST /api/mark-messages-read
// ---------------------------------------------------------------------------

describe('markMessagesAsRead', function () {

    it('marks unread messages as read for an authorised member', function () {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($user);

        $circle = Circle::create([
            'user_owner_id' => $user->id,
            'name'          => 'Mark Read Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $user->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'circle_id'     => $circle->id,
            'title'         => 'Mark Read Circle',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        $chat = ConversationChat::create([
            'init_user_id'       => $other->id,
            'conversation_id'    => $conversation->id,
            'content'            => 'Unread',
            'type'               => 'chat',
            'seen_by_other_user' => 'false',
        ]);

        $response = $this->postJson('/api/mark-messages-read', [
            'conversation_id' => $conversation->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('updated_count', 1);

        $this->assertDatabaseHas('conversation_chats', [
            'id'                 => $chat->id,
            'seen_by_other_user' => 'true',
        ]);
    });

    it('returns 403 when user is not a circle member', function () {
        $owner    = User::factory()->create();
        $outsider = User::factory()->create();
        Sanctum::actingAs($outsider);

        $circle = Circle::create([
            'user_owner_id' => $owner->id,
            'name'          => 'Forbidden Circle',
            'type'          => 'private_circle',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $owner->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $conversation = Conversation::create([
            'owner_user_id' => $owner->id,
            'circle_id'     => $circle->id,
            'title'         => 'Forbidden',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        $this->postJson('/api/mark-messages-read', [
            'conversation_id' => $conversation->id,
        ])->assertStatus(403);
    });

    it('returns 422 when conversation_id is missing', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/mark-messages-read', [])->assertStatus(422);
    });

    it('does not mark the current user\'s own messages as read', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $circle = Circle::create([
            'user_owner_id' => $user->id,
            'name'          => 'Own Msg Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $user->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $conversation = Conversation::create([
            'owner_user_id' => $user->id,
            'circle_id'     => $circle->id,
            'title'         => 'Own Msg Circle',
            'type'          => 'group',
            'status'        => 'active',
        ]);

        $chat = ConversationChat::create([
            'init_user_id'       => $user->id,
            'conversation_id'    => $conversation->id,
            'content'            => 'My own',
            'type'               => 'chat',
            'seen_by_other_user' => 'false',
        ]);

        $this->postJson('/api/mark-messages-read', [
            'conversation_id' => $conversation->id,
        ])->assertStatus(200)
          ->assertJsonPath('updated_count', 0);

        $this->assertDatabaseHas('conversation_chats', [
            'id'                 => $chat->id,
            'seen_by_other_user' => 'false',
        ]);
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/mark-messages-read', [
            'conversation_id' => 1,
        ])->assertStatus(401);
    });
});
