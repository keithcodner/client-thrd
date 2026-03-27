<?php

use App\Models\User;
use App\Models\Circles\Circle;
use App\Models\Circles\CircleMemberTracker;
use App\Models\Circles\CircleRequest;
use App\Models\Notification;
use Laravel\Sanctum\Sanctum;

// ---------------------------------------------------------------------------
// POST /api/search-users
// ---------------------------------------------------------------------------

describe('searchUsers', function () {

    it('returns matching users by name', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $target = User::factory()->create(['name' => 'Alice Wonder']);

        $response = $this->postJson('/api/search-users', [
            'query' => 'Alice',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['users'])
            ->assertJsonFragment(['id' => $target->id]);
    });

    it('excludes the current authenticated user from results', function () {
        $user = User::factory()->create(['name' => 'SelfSearch']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/search-users', [
            'query' => 'SelfSearch',
        ]);

        $response->assertStatus(200);
        $ids = collect($response->json('users'))->pluck('id')->toArray();
        expect($ids)->not->toContain($user->id);
    });

    it('returns at most 10 results', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        User::factory()->count(15)->create(['name' => 'SearchMe User']);

        $response = $this->postJson('/api/search-users', [
            'query' => 'SearchMe',
        ]);

        $response->assertStatus(200);
        expect($response->json('users'))->toHaveCount(10);
    });

    it('returns 422 when query is missing', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/search-users', [])->assertStatus(422);
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/search-users', [
            'query' => 'Alice',
        ])->assertStatus(401);
    });
});

// ---------------------------------------------------------------------------
// POST /api/send-circle-invite
// ---------------------------------------------------------------------------

describe('sendCircleChatInvite', function () {

    it('sends a circle invite successfully', function () {
        $owner  = User::factory()->create();
        $target = User::factory()->create();
        Sanctum::actingAs($owner);

        $circle = Circle::create([
            'user_owner_id' => $owner->id,
            'name'          => 'Invite Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $owner->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $response = $this->postJson('/api/send-circle-invite', [
            'circle_id'       => $circle->id,
            'invited_user_id' => $target->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Circle invite sent successfully.');

        $this->assertDatabaseHas('circles_requests', [
            'circle_id'                   => $circle->id,
            'requesting_to_join_user_id'  => $target->id,
            'status'                      => 'pending',
        ]);
    });

    it('creates a notification for the invited user', function () {
        $owner  = User::factory()->create();
        $target = User::factory()->create();
        Sanctum::actingAs($owner);

        $circle = Circle::create([
            'user_owner_id' => $owner->id,
            'name'          => 'Notify Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $owner->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $this->postJson('/api/send-circle-invite', [
            'circle_id'       => $circle->id,
            'invited_user_id' => $target->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $target->id,
            'from_id' => $owner->id,
            'type'    => 'circle_request',
        ]);
    });

    it('returns 400 when the user is already a member', function () {
        $owner  = User::factory()->create();
        $member = User::factory()->create();
        Sanctum::actingAs($owner);

        $circle = Circle::create([
            'user_owner_id' => $owner->id,
            'name'          => 'Member Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $member->id,
            'type'      => 'member',
            'status'    => 'active',
        ]);

        $this->postJson('/api/send-circle-invite', [
            'circle_id'       => $circle->id,
            'invited_user_id' => $member->id,
        ])->assertStatus(400)
          ->assertJsonPath('message', 'This user is already a member of the circle.');
    });

    it('returns 400 when a pending invite already exists', function () {
        $owner  = User::factory()->create();
        $target = User::factory()->create();
        Sanctum::actingAs($owner);

        $circle = Circle::create([
            'user_owner_id' => $owner->id,
            'name'          => 'Dup Invite Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleRequest::create([
            'circle_id'                  => $circle->id,
            'requester_user_id'          => $owner->id,
            'requesting_to_join_user_id' => $target->id,
            'type'                       => 'circle_request',
            'status'                     => 'pending',
        ]);

        $this->postJson('/api/send-circle-invite', [
            'circle_id'       => $circle->id,
            'invited_user_id' => $target->id,
        ])->assertStatus(400)
          ->assertJsonPath('message', 'A pending invite already exists for this user.');
    });

    it('returns 422 when circle_id is missing', function () {
        $user   = User::factory()->create();
        $target = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/send-circle-invite', [
            'invited_user_id' => $target->id,
        ])->assertStatus(422);
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/send-circle-invite', [
            'circle_id'       => 1,
            'invited_user_id' => 1,
        ])->assertStatus(401);
    });
});

// ---------------------------------------------------------------------------
// POST /api/accept-circle-invite
// ---------------------------------------------------------------------------

describe('acceptCircleChatInvite', function () {

    it('accepts a circle invite and adds user to circle members', function () {
        $inviter  = User::factory()->create();
        $invitee  = User::factory()->create();
        Sanctum::actingAs($invitee);

        $circle = Circle::create([
            'user_owner_id' => $inviter->id,
            'name'          => 'Accept Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        $circleRequest = CircleRequest::create([
            'circle_id'                  => $circle->id,
            'requester_user_id'          => $inviter->id,
            'requesting_to_join_user_id' => $invitee->id,
            'type'                       => 'circle_request',
            'status'                     => 'pending',
        ]);

        $response = $this->postJson('/api/accept-circle-invite', [
            'request_id' => $circleRequest->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Circle invite accepted successfully.');

        $this->assertDatabaseHas('circles_requests', [
            'id'     => $circleRequest->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('circles_member_tracker', [
            'circle_id' => $circle->id,
            'user_id'   => $invitee->id,
            'type'      => 'member',
            'status'    => 'active',
        ]);
    });

    it('creates a notification for the inviter on acceptance', function () {
        $inviter = User::factory()->create();
        $invitee = User::factory()->create();
        Sanctum::actingAs($invitee);

        $circle = Circle::create([
            'user_owner_id' => $inviter->id,
            'name'          => 'Notify Accept',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        $circleRequest = CircleRequest::create([
            'circle_id'                  => $circle->id,
            'requester_user_id'          => $inviter->id,
            'requesting_to_join_user_id' => $invitee->id,
            'type'                       => 'circle_request',
            'status'                     => 'pending',
        ]);

        $this->postJson('/api/accept-circle-invite', [
            'request_id' => $circleRequest->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $inviter->id,
            'from_id' => $invitee->id,
            'type'    => 'circle_request',
        ]);
    });

    it('returns 403 when a different user tries to accept an invite', function () {
        $inviter    = User::factory()->create();
        $invitee    = User::factory()->create();
        $thirdParty = User::factory()->create();
        Sanctum::actingAs($thirdParty);

        $circle = Circle::create([
            'user_owner_id' => $inviter->id,
            'name'          => 'Third Party Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        $circleRequest = CircleRequest::create([
            'circle_id'                  => $circle->id,
            'requester_user_id'          => $inviter->id,
            'requesting_to_join_user_id' => $invitee->id,
            'type'                       => 'circle_request',
            'status'                     => 'pending',
        ]);

        $this->postJson('/api/accept-circle-invite', [
            'request_id' => $circleRequest->id,
        ])->assertStatus(403)
          ->assertJsonPath('message', 'Unauthorized action.');
    });

    it('returns 400 when invite is already accepted', function () {
        $inviter = User::factory()->create();
        $invitee = User::factory()->create();
        Sanctum::actingAs($invitee);

        $circle = Circle::create([
            'user_owner_id' => $inviter->id,
            'name'          => 'Already Accepted',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        $circleRequest = CircleRequest::create([
            'circle_id'                  => $circle->id,
            'requester_user_id'          => $inviter->id,
            'requesting_to_join_user_id' => $invitee->id,
            'type'                       => 'circle_request',
            'status'                     => 'accepted',
        ]);

        $this->postJson('/api/accept-circle-invite', [
            'request_id' => $circleRequest->id,
        ])->assertStatus(400)
          ->assertJsonPath('message', 'This invite has already been accepted.');
    });

    it('returns 422 when request_id does not exist', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/accept-circle-invite', [
            'request_id' => 999999,
        ])->assertStatus(422);
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/accept-circle-invite', [
            'request_id' => 1,
        ])->assertStatus(401);
    });
});

// ---------------------------------------------------------------------------
// POST /api/deny-circle-invite
// ---------------------------------------------------------------------------

describe('denyCircleChatInvite', function () {

    it('declines a pending circle invite', function () {
        $inviter = User::factory()->create();
        $invitee = User::factory()->create();
        Sanctum::actingAs($invitee);

        $circle = Circle::create([
            'user_owner_id' => $inviter->id,
            'name'          => 'Deny Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        $circleRequest = CircleRequest::create([
            'circle_id'                  => $circle->id,
            'requester_user_id'          => $inviter->id,
            'requesting_to_join_user_id' => $invitee->id,
            'type'                       => 'circle_request',
            'status'                     => 'pending',
        ]);

        $this->postJson('/api/deny-circle-invite', [
            'request_id' => $circleRequest->id,
        ])->assertStatus(200)
          ->assertJsonPath('success', true)
          ->assertJsonPath('message', 'Circle invite declined successfully.');

        $this->assertDatabaseHas('circles_requests', [
            'id'     => $circleRequest->id,
            'status' => 'declined',
        ]);
    });

    it('returns 403 when a different user tries to deny an invite', function () {
        $inviter    = User::factory()->create();
        $invitee    = User::factory()->create();
        $thirdParty = User::factory()->create();
        Sanctum::actingAs($thirdParty);

        $circle = Circle::create([
            'user_owner_id' => $inviter->id,
            'name'          => 'Third Party Deny',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        $circleRequest = CircleRequest::create([
            'circle_id'                  => $circle->id,
            'requester_user_id'          => $inviter->id,
            'requesting_to_join_user_id' => $invitee->id,
            'type'                       => 'circle_request',
            'status'                     => 'pending',
        ]);

        $this->postJson('/api/deny-circle-invite', [
            'request_id' => $circleRequest->id,
        ])->assertStatus(403);
    });

    it('returns 400 when invite is already declined', function () {
        $inviter = User::factory()->create();
        $invitee = User::factory()->create();
        Sanctum::actingAs($invitee);

        $circle = Circle::create([
            'user_owner_id' => $inviter->id,
            'name'          => 'Already Declined',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        $circleRequest = CircleRequest::create([
            'circle_id'                  => $circle->id,
            'requester_user_id'          => $inviter->id,
            'requesting_to_join_user_id' => $invitee->id,
            'type'                       => 'circle_request',
            'status'                     => 'declined',
        ]);

        $this->postJson('/api/deny-circle-invite', [
            'request_id' => $circleRequest->id,
        ])->assertStatus(400)
          ->assertJsonPath('message', 'This invite has already been declined.');
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/deny-circle-invite', [
            'request_id' => 1,
        ])->assertStatus(401);
    });
});

// ---------------------------------------------------------------------------
// POST /api/get-pending-circle-invites
// ---------------------------------------------------------------------------

describe('getPendingCircleInvites', function () {

    it('returns pending invite user ids for a circle member', function () {
        $owner  = User::factory()->create();
        $target = User::factory()->create();
        Sanctum::actingAs($owner);

        $circle = Circle::create([
            'user_owner_id' => $owner->id,
            'name'          => 'Pending Invite Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $owner->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        CircleRequest::create([
            'circle_id'                  => $circle->id,
            'requester_user_id'          => $owner->id,
            'requesting_to_join_user_id' => $target->id,
            'type'                       => 'circle_request',
            'status'                     => 'pending',
        ]);

        $response = $this->postJson('/api/get-pending-circle-invites', [
            'circle_id' => $circle->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['pending_user_ids' => [$target->id]]);
    });

    it('returns 403 when the user is not a member of the circle', function () {
        $owner    = User::factory()->create();
        $outsider = User::factory()->create();
        Sanctum::actingAs($outsider);

        $circle = Circle::create([
            'user_owner_id' => $owner->id,
            'name'          => 'Forbidden Pending',
            'type'          => 'private_circle',
            'status'        => 'active',
        ]);

        $this->postJson('/api/get-pending-circle-invites', [
            'circle_id' => $circle->id,
        ])->assertStatus(403);
    });

    it('returns 422 when circle_id is missing', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/get-pending-circle-invites', [])->assertStatus(422);
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/get-pending-circle-invites', [
            'circle_id' => 1,
        ])->assertStatus(401);
    });
});

// ---------------------------------------------------------------------------
// POST /api/get-circle-members
// ---------------------------------------------------------------------------

describe('getCircleMembers', function () {

    it('returns all active members of a circle', function () {
        $owner  = User::factory()->create();
        $member = User::factory()->create();
        Sanctum::actingAs($owner);

        $circle = Circle::create([
            'user_owner_id' => $owner->id,
            'name'          => 'Members Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $owner->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $member->id,
            'type'      => 'member',
            'status'    => 'active',
        ]);

        $response = $this->postJson('/api/get-circle-members', [
            'circle_id' => $circle->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'members');

        $memberIds = collect($response->json('members'))->pluck('id')->toArray();
        expect($memberIds)->toContain($owner->id);
        expect($memberIds)->toContain($member->id);
    });

    it('returns member data with id, name, email, and type fields', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $circle = Circle::create([
            'user_owner_id' => $user->id,
            'name'          => 'Fields Circle',
            'type'          => 'community_hub',
            'status'        => 'active',
        ]);

        CircleMemberTracker::create([
            'circle_id' => $circle->id,
            'user_id'   => $user->id,
            'type'      => 'owner',
            'status'    => 'active',
        ]);

        $response = $this->postJson('/api/get-circle-members', [
            'circle_id' => $circle->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'members' => [
                    '*' => ['id', 'name', 'email', 'type', 'joined_at'],
                ],
            ]);
    });

    it('returns 403 when the user is not a member of the circle', function () {
        $owner    = User::factory()->create();
        $outsider = User::factory()->create();
        Sanctum::actingAs($outsider);

        $circle = Circle::create([
            'user_owner_id' => $owner->id,
            'name'          => 'Forbidden Members',
            'type'          => 'private_circle',
            'status'        => 'active',
        ]);

        $this->postJson('/api/get-circle-members', [
            'circle_id' => $circle->id,
        ])->assertStatus(403);
    });

    it('returns 422 when circle_id is missing', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/get-circle-members', [])->assertStatus(422);
    });

    it('returns 422 when circle_id does not exist', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/get-circle-members', [
            'circle_id' => 999999,
        ])->assertStatus(422);
    });

    it('returns 401 for unauthenticated request', function () {
        $this->postJson('/api/get-circle-members', [
            'circle_id' => 1,
        ])->assertStatus(401);
    });
});
