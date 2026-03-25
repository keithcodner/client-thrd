---
name: websocket-channels
description: "Implement Laravel broadcasting channels (private or presence) with Soketi and pusher-js React Native. Use when: adding a new WebSocket channel, debugging 403 on channel auth, wiring up presence/online status, defining channel callbacks in channels.php, configuring auth endpoints, or subscribing to channels in the mobile app."
---

# WebSocket Channel Implementation

This skill covers the complete pattern for adding private and presence channels in this project (Laravel + Soketi + Expo React Native).

## Stack

- **Server**: Laravel 11, Soketi (self-hosted, port 6001), Sanctum auth
- **Client**: pusher-js/react-native, `websocketService.ts`, `AuthContext.tsx`
- **Auth endpoint**: `http://<host>:8000/broadcasting/auth` (no `/api` prefix — registered in `BroadcastServiceProvider`, not `api.php`)

---

## Critical Rules (learn from past bugs)

### 1. Channel name prefix stripping
Laravel strips `private-` and `presence-` from channel names **before** matching callbacks in `channels.php`.

| Client subscribes to | Callback name in `channels.php` |
|---|---|
| `private-sitePrivateChat.12` | `sitePrivateChat.{id}` |
| `private-typing.12` | `typing.{id}` |
| `presence-conversation.12` | `conversation.{id}` ← NOT `presence-conversation.{id}` |

**Never include the `private-` or `presence-` prefix in your `Broadcast::channel(...)` definition.**

### 2. Auth endpoint has NO `/api` prefix
`Broadcast::routes()` is called in `BroadcastServiceProvider::boot()` — not inside the `api` route group in `api.php`. This means the endpoint is `/broadcasting/auth`, not `/api/broadcasting/auth`.

```typescript
// mobile/services/websocketService.ts — CORRECT
authEndpoint: `${PUSHER_CONFIG.apiUrl}/broadcasting/auth`,
```

### 3. Never call `Broadcast::routes()` twice
It is already called in `BroadcastServiceProvider`. Do NOT add it to `routes/api.php`. A second call overrides channel definitions from the first.

### 4. Presence callbacks must return a user array, not a boolean
Private channels: returning `true` is enough.
Presence channels: must return an array with at least `id` and `name`, or `false` to deny.

```php
// ✅ Presence — must return array
return ['id' => $user->id, 'name' => $user->name];

// ❌ Wrong — returning true silently fails for presence
return true;
```

### 5. Guard against stale/undefined auth token
`websocketService.ts` tracks `currentToken`. If `connect()` is called before the session is ready, the guard prevents Pusher being initialized with `undefined`. This matters because presence channels always hit the auth endpoint — unlike private channels which can sometimes cache.

---

## Adding a New Private Channel

### Step 1 — Define callback in `api/routes/channels.php`

```php
Broadcast::channel('myChannel.{resourceId}', function ($user, $resourceId) {
    // Check if $user is allowed access to $resourceId
    $allowed = MyModel::where('id', $resourceId)
        ->where('user_id', $user->id)
        ->exists();
    return $allowed;
});
```

### Step 2 — Broadcast the event from Laravel

```php
// app/Events/MyEvent.php
use Illuminate\Broadcasting\PrivateChannel;

public function broadcastOn(): array
{
    return [new PrivateChannel("myChannel.{$this->resourceId}")];
}
```

### Step 3 — Subscribe in `websocketService.ts`

```typescript
subscribeToMyChannel(resourceId: string, onEvent: (data: any) => void) {
    if (!this.pusher) return null;

    const channelName = `private-myChannel.${resourceId}`;
    if (this.channels.has(channelName)) return this.channels.get(channelName);

    const channel = this.pusher.subscribe(channelName);
    channel.bind('myEventName', onEvent);
    this.channels.set(channelName, channel);
    return channel;
}
```

---

## Adding a New Presence Channel

### Step 1 — Define callback in `api/routes/channels.php`

```php
// NOTE: name must NOT include 'presence-' prefix
Broadcast::channel('myPresenceChannel.{resourceId}', function ($user, $resourceId) {
    $isMember = MyMemberModel::where('resource_id', $resourceId)
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->exists();

    if (!$isMember) return false;

    // REQUIRED: return user info array for presence
    return [
        'id'   => $user->id,
        'name' => $user->name,
    ];
});
```

### Step 2 — Subscribe in `websocketService.ts`

```typescript
subscribeToMyPresence(
    resourceId: string,
    onJoined: (member: any) => void,
    onLeft: (member: any) => void,
    onMemberList: (members: any[]) => void,
    onError?: (error: any) => void
) {
    if (!this.pusher) {
        if (onError) onError({ message: 'WebSocket not connected' });
        return null;
    }

    // Client uses 'presence-' prefix; server callback omits it
    const channelName = `presence-myPresenceChannel.${resourceId}`;
    if (this.channels.has(channelName)) return this.channels.get(channelName);

    const channel = this.pusher.subscribe(channelName);

    channel.bind('pusher:subscription_error', (error: any) => {
        if (onError) onError(error);
    });

    channel.bind('pusher:subscription_succeeded', (members: any) => {
        const memberList = Object.keys(members.members).map(userId => ({
            id: parseInt(userId),
            ...members.members[userId],
        }));
        onMemberList(memberList);
    });

    channel.bind('pusher:member_added', (member: any) => {
        onJoined({ id: parseInt(member.id), ...member.info });
    });

    channel.bind('pusher:member_removed', (member: any) => {
        onLeft({ id: parseInt(member.id), ...member.info });
    });

    this.channels.set(channelName, channel);
    return channel;
}
```

### Step 3 — Wire into `AuthContext.tsx` (optional, for global online state)

Follow the pattern of `subscribeToConversationPresence` / `unsubscribeFromConversationPresence` in `mobile/context/AuthContext.tsx`.

---

## BroadcastServiceProvider (do not change)

```php
// api/app/Providers/BroadcastServiceProvider.php
public function boot()
{
    Broadcast::routes(['middleware' => ['auth:sanctum']]);
    require base_path('routes/channels.php');
}
```

This is the one and only place `Broadcast::routes()` is called. Do not move it or duplicate it.

---

## Debugging 403 Checklist

Work through these in order:

1. **Is the callback name correct?** Remember Laravel strips `private-`/`presence-` before matching. `presence-conversation.12` → look for `conversation.{id}`.
2. **Is `Broadcast::routes()` called only once?** Check `BroadcastServiceProvider` and `api.php` — it must not appear in both.
3. **Is the auth endpoint correct?** Should be `/broadcasting/auth` (no `/api` prefix).
4. **Is the token valid?** Check `Authorization: Bearer ...` header is present and not `undefined`. Add logging: `\Log::info('auth headers', request()->headers->all())`.
5. **Does the callback return the right type?** Presence needs an array; returning `true` or `false` both cause 403 in presence channels.
6. **Clear caches**: `php artisan cache:clear && php artisan route:clear`

---

## Reference Files

- `api/routes/channels.php` — all channel callbacks
- `api/app/Providers/BroadcastServiceProvider.php` — route registration
- `mobile/services/websocketService.ts` — Pusher wrapper / subscribe methods
- `mobile/context/AuthContext.tsx` — presence subscribe/unsubscribe wired to global state
- `mobile/docs/WEBSOCKETS_REALTIME_MESSAGING.md` — full troubleshooting, especially Issue 7 (403 on presence)
