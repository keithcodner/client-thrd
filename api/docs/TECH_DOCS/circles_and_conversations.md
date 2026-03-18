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
   - **Purpose:** Tracks members of circles.
   - **Fields:**
     - `circle_id`: Foreign key referencing `circles`.
     - `user_id`: ID of the user who joined the circle.
     - `type`, `status`: Metadata.

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

This document provides an overview of how circles and conversations are structured and their relationships in the database.