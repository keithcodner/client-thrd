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
   - **Purpose:** Tracks requests to join circles.
   - **Fields:**
     - `circle_id`: Foreign key referencing `circles`.
     - `requesting_to_join_user_id`: ID of the user requesting to join.
     - `type`, `status`: Metadata.

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

This document provides an overview of how circles and conversations are structured and their relationships in the database.