# Circles and Conversations Documentation

## Circles
Circles represent groups created by users. Below are the details of the tables and their relationships:

### Tables

1. **`circles`**
   - **Purpose:** Tracks ownership and metadata of circle groups.
   - **Fields:**
     - `id`: Primary key.
     - `user_owner_id`: ID of the user who owns the circle.
     - `name`: Name of the circle.
     - `type`: Type of the circle (e.g., standard).
     - `status`: Status of the circle (e.g., active, inactive).
     - `created_at`, `updated_at`: Timestamps.

2. **`circles_details`**
   - **Purpose:** Tracks detailed attributes of circles.
   - **Fields:**
     - `circle_id`: Foreign key referencing `circles`.
     - `description`: Description of the circle.
     - `style_code`: Visual style of the circle (e.g., sage, stone).
     - `privacy_state`: Privacy settings of the circle.
     - `type`, `status`: Additional metadata.

3. **`circles_idea_board`**
   - **Purpose:** Tracks posts to a circle's idea board.
   - **Fields:**
     - `circle_id`: Foreign key referencing `circles`.
     - `details`: Content of the idea board.
     - `type`, `status`: Metadata.

4. **`circles_idea_board_posts`**
   - **Purpose:** Tracks posts on the idea board.
   - **Fields:**
     - `circles_idea_board_id`: Foreign key referencing `circles_idea_board`.
     - `user_owner_id`: ID of the user who created the post.
     - `name`, `type`, `status`: Metadata.

5. **`circles_member_tracker`**
   - **Purpose:** Tracks members of circles and their membership status.
   - **Fields:**
     - `circle_id`: Foreign key referencing `circles`.
     - `user_id`: ID of the user who joined the circle.
     - `type`: Member type (e.g., owner, user).
     - `status`: Membership status:
       - `active`: User is actively joined and can participate in the circle.
       - `inactive`: User has left the circle and cannot participate.
   - **Behavior:**
     - When retrieving circles for a user, only circles where the user's status is `active` are returned.
     - Inactive members are excluded from circle data and cannot access circle conversations.

6. **`circles_requests`**
   - **Purpose:** Tracks requests/invites for users to join circles.
   - **Fields:**
     - `id`: Primary key.
     - `circle_id`: Foreign key referencing `circles`.
     - `requester_user_id`: ID of the user who initiated the invite/request.
     - `requesting_to_join_user_id`: ID of the user who is being invited to join.
     - `type`: Type of request.
       - **Enum values:**
         - `circle_request`: Standard invite to join a circle.
     - `status`: Status of the request.
       - **Enum values:**
         - `pending`: Request has been sent but not yet acted upon.
         - `accepted`: User has accepted the invite and joined the circle.
         - `declined`: User has declined the invite.
     - `created_at`, `updated_at`: Timestamps.
   - **Behavior:**
     - When a circle owner/admin sends an invite, a record is created with status `pending`.
     - When the invited user accepts, status changes to `accepted` and a record is added to `circles_member_tracker`.
     - When the invited user declines, status changes to `declined`.

7. **`notifications`**
   - **Purpose:** Tracks notifications for various system events including circle invites.
   - **Fields:**
     - `id`: Primary key.
     - `user_id`: ID of the user who receives the notification.
     - `from_id`: ID of the user who triggered the notification (0 for system).
     - `fk_circle_item_post_id`: Foreign key referencing circles (when applicable).
     - `type`: Type of notification.
       - **Enum values (circle-related):**
         - `circle_request`: Notification about a circle invite.
     - `title`: Title of the notification.
     - `comment`: Detailed message/comment for the notification.
     - `status`: Read status of the notification.
       - **Enum values:**
         - `unread`: Notification has not been read.
         - `read`: Notification has been read.
     - `color_status`: Hex color code for visual styling (randomly assigned from predefined set).
     - `created_at`, `updated_at`: Timestamps.

---

## Conversations
Conversations represent communication between users or within circles. Below are the details of the tables and their relationships:

### Tables

1. **`conversations`**
   - **Purpose:** Tracks conversations between users or within circles.
   - **Fields:**
     - `owner_user_id`: ID of the user who initiated the conversation.
     - `to_id`: ID of the recipient (for one-to-one conversations).
     - `circle_id`: ID of the circle (for group conversations).
     - `title`, `content`: Metadata and content of the conversation.
     - `type`: Type of conversation (e.g., couple, circle).
     - `status`: Status of the conversation (e.g., active, inactive).

2. **`conversation_chats`**
   - **Purpose:** Tracks individual chats within a conversation.
   - **Fields:**
     - `init_user_id`: ID of the user who initiated the chat.
     - `end_user_id`: ID of the recipient.
     - `conversation_id`: Foreign key referencing `conversations`.
     - `content`: Content of the chat.
     - `attachment`: Any attachments in the chat.

---

## Relationships
- **Circles:**
  - A circle can have multiple details, idea boards, and members.
  - Members can request to join circles.

- **Conversations:**
  - Conversations can be one-to-one or group-based (within circles).
  - Chats belong to conversations and track individual messages.

---

## API Endpoints

### Get User Circle Data
**Endpoint:** `POST /api/user-circles`

**Route Name:** `get-user-circles`

**Description:** Retrieves all circles that the authenticated user is actively a member of.

**Authentication:** Required (uses Auth::user())

**Middleware:** `TrackUserActivity`

**Request:** No request body required

**Response:**
```json
{
  "circles": [
    {
      "id": 1,
      "name": "Circle Name",
      "type": "community_hub",
      "status": "active",
      "created_at": "2026-03-18T19:40:01.000000Z",
      "updated_at": "2026-03-18T19:40:01.000000Z",
      "details": {
        "description": "Circle description",
        "style_code": "sage",
        "privacy_state": "public",
        "type": "community_hub"
      },
      "ideaBoard": {
        "id": 1,
        "circle_id": 1,
        "details": null,
        "type": "default",
        "status": "active"
      },
      "members": [
        {
          "id": 1,
          "circle_id": 1,
          "user_id": 86,
          "type": "owner",
          "status": "active"
        }
      ]
    }
  ]
}
```

**Logic:**
- Only returns circles where the user's membership status in `circles_member_tracker` is `active`.
- Excludes circles where the user's status is `inactive` (user has left the circle).
- Includes only active members in the member list.

**Frontend Integration:**
- Service: `mobile/services/chatService.ts` - `getUserCircleData()`
- Component: `mobile/app/(app)/(tabs)/(chat)/index.tsx`
- Data is transformed to `ChatItemData` format for display:
  ```typescript
  {
    id: circle.id.toString(),
    name: circle.name,
    lastMessage: 'No messages yet',
    timestamp: formatted_time,
    unread: false
  }
  ```
- A hardcoded THRD chat appears first, followed by user's circles.

---

### Send Circle Invite
**Endpoint:** `POST /api/send-circle-invite`

**Route Name:** `send-circle-invite`

**Description:** Sends an invite to a user to join a circle.

**Authentication:** Required (uses Auth::user())

**Middleware:** `TrackUserActivity`

**Request:**
```json
{
  "circle_id": 1,
  "invited_user_id": 42
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Circle invite sent successfully.",
  "request": {
    "id": 1,
    "circle_id": 1,
    "requester_user_id": 86,
    "requesting_to_join_user_id": 42,
    "type": "circle_request",
    "status": "pending"
  }
}
```

**Response (Error - Already Invited):**
```json
{
  "success": false,
  "message": "An invite has already been sent to this user."
}
```

**Logic:**
- Creates a record in `circles_requests` with status `pending` and type `circle_request`.
- Creates a notification for the invited user with type `circle_request`.
- Checks for duplicate pending invites before creating.

**Frontend Integration:**
- Service: `mobile/services/chatService.ts` - `sendCircleInvite()`
- Component: `mobile/components/chat/CircleInfoModal.tsx`

---

### Accept Circle Invite
**Endpoint:** `POST /api/accept-circle-invite`

**Route Name:** `accept-circle-invite`

**Description:** Accepts a circle invite and adds the user to the circle.

**Authentication:** Required (uses Auth::user())

**Middleware:** `TrackUserActivity`

**Request:**
```json
{
  "request_id": 1
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Circle invite accepted successfully."
}
```

**Response (Error - Unauthorized):**
```json
{
  "success": false,
  "message": "Unauthorized action."
}
```

**Logic:**
- Updates the `circles_requests` record status to `accepted`.
- Creates a record in `circles_member_tracker` with type `member` and status `active`.
- Creates a notification for the requester confirming the invite was accepted.
- Verifies the authenticated user is the one who was invited.

**Frontend Integration:**
- Service: `mobile/services/chatService.ts` - `acceptCircleInvite()`
- Component: To be implemented (notifications screen)

---

### Deny Circle Invite
**Endpoint:** `POST /api/deny-circle-invite`

**Route Name:** `deny-circle-invite`

**Description:** Denies/declines a circle invite.

**Authentication:** Required (uses Auth::user())

**Middleware:** `TrackUserActivity`

**Request:**
```json
{
  "request_id": 1
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Circle invite declined successfully."
}
```

**Response (Error - Unauthorized):**
```json
{
  "success": false,
  "message": "Unauthorized action."
}
```

**Logic:**
- Updates the `circles_requests` record status to `declined`.
- Verifies the authenticated user is the one who was invited.
- No notification is sent to the requester when an invite is declined.

**Frontend Integration:**
- Service: `mobile/services/chatService.ts` - `denyCircleInvite()`
- Component: To be implemented (notifications screen)

---

### Search Users for Invite
**Endpoint:** `POST /api/search-users`

**Route Name:** `search-users`

**Description:** Searches for users to invite to a circle.

**Authentication:** Required (uses Auth::user())

**Middleware:** `TrackUserActivity`

**Request:**
```json
{
  "query": "john"
}
```

**Response:**
```json
{
  "success": true,
  "users": [
    {
      "id": 42,
      "name": "John Doe",
      "firstname": "John",
      "lastname": "Doe",
      "username": "johndoe",
      "avatar": null
    }
  ]
}
```

**Logic:**
- Searches users by name, firstname, lastname, or username.
- Excludes the current authenticated user from results.
- Filters out users without at least one of: firstname, name, or username.
- Limits results to 10 users.

**Frontend Integration:**
- Service: `mobile/services/chatService.ts` - `searchUsersForInvite()`
- Component: `mobile/components/chat/CircleInfoModal.tsx`

---

## Enums Reference

### circles_member_tracker.type
- `owner`: User is the circle owner
- `member`: Regular member of the circle

### circles_member_tracker.status
- `active`: User is actively part of the circle
- `inactive`: User has left the circle

### circles_requests.type
- `circle_request`: Standard invite to join a circle

### circles_requests.status
- `pending`: Request/invite is awaiting response
- `accepted`: User accepted the invite
- `declined`: User declined the invite

### notifications.type (circle-related)
- `circle_request`: Notification about a circle invite

### notifications.status
- `unread`: Notification not yet read
- `read`: Notification has been read

### notifications.color_status
Random hex color from predefined set:
- `#8B7355` (sage brown)
- `#6B7280` (stone gray)
- `#92400E` (clay brown)
- `#D97706` (amber)
- `#7C2D12` (dusk brown)
- `#B45309` (sand orange)
- `#059669` (emerald)
- `#0891B2` (cyan)
- `#4F46E5` (indigo)
- `#7C3AED` (violet)

---

This document provides an overview of how circles and conversations are structured and their relationships in the database.