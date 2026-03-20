# Chat Messaging System

## Overview
The THRD chat messaging system supports both 1-to-1 conversations and circle-based group chats with real-time broadcasting capabilities.

## Message Types
The system supports three types of messages:

1. **chat** - Regular user messages (default)
2. **announcement** - System notifications for user events (joins, leaves, changes)
3. **system** - Tutorial or admin messages

## Sending Messages

### API Endpoint
**POST** `/post-chat`

### Request Body
```typescript
{
  conversation_id: number;     // Required: The conversation ID
  content: string;             // Required: Message content (max 5000 chars)
  type?: "chat" | "announcement" | "system";  // Optional: Message type (default: "chat")
  end_user_id?: number;        // Required for 1-to-1, not used for circles
}
```

### Response
```typescript
{
  message: string;             // Success message
  chat: {                      // Created chat object
    id: number;
    init_user_id: number;
    conversation_id: number;
    content: string;
    type: string;
    // ... other fields
  }
}
```

## Frontend Implementation

### Using the Chat Service

```typescript
import { sendMessage, SendMessageData } from "@/services/chatService";

// Send a message
const messageData: SendMessageData = {
  conversation_id: 123,
  content: "Hello, world!",
  type: "chat",  // optional
};

try {
  const response = await sendMessage(messageData);
  console.log("Message sent:", response.chat);
} catch (error) {
  console.error("Failed to send message:", error);
}
```

### Optimistic UI Updates
The chat interface implements optimistic UI updates for instant feedback:

1. Message appears immediately in the UI
2. API request sent in background
3. Temporary message ID replaced with server ID on success
4. Message removed if sending fails

```typescript
// Create optimistic message
const optimisticMessage: MessageData = {
  id: `temp-${Date.now()}`,
  sender: user.name,
  senderId: user.id,
  content: messageContent,
  timestamp: getCurrentTime(),
  isSystemMessage: false,
  isCurrentUser: true,
};

// Add to UI immediately
setMessages(prev => [...prev, optimisticMessage]);

// Send to API
const response = await sendMessage({...});

// Update with real ID
setMessages(prev => 
  prev.map(msg => 
    msg.id === optimisticMessage.id 
      ? { ...msg, id: response.chat.id.toString() }
      : msg
  )
);
```

## Message Display

### Message Types Display

**System Messages** (left-aligned with card background):
- Avatar with first initial
- Uppercase sender name
- Card-styled message bubble
- Timestamp

**Current User Messages** (right-aligned with primary color):
- Green primary color background
- White text
- Avatar on right side
- Timestamp below

**Other Users' Messages** (left-aligned with card background):
- Avatar on left side
- Sender name above message
- Card-styled message bubble
- Timestamp below

### Avatar Generation
Avatars display the first initial of the user's name:
```typescript
const getInitials = (name: string) => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 1);
};
```

## Backend Implementation

### Controller Method
`ChatController::postChat(Request $request)`

**Validation:**
- conversation_id: required, exists in conversations table
- content: required, max 5000 characters
- type: optional, must be 'chat', 'announcement', or 'system'
- end_user_id: optional, required for 1-to-1 conversations

**Logic:**
1. Validates request data
2. Fetches conversation to determine type (couple vs group)
3. For TYPE_COUPLE: requires end_user_id and prevents self-messaging
4. For TYPE_GROUP: no end_user_id needed (first-come-first-serve reading)
5. Creates ConversationChat record
6. Broadcasts NewChatMessage event to other users
7. Returns created chat object

### Database Model
`ConversationChat` model fields:
- `init_user_id` - User who sent the message
- `end_user_id` - Recipient (1-to-1 only)
- `conversation_id` - Parent conversation
- `content` - Message text
- `type` - Message type (chat/announcement/system)
- `seen_by_other_user` - Read status
- `seen_by_received_user` - Read status

### Broadcasting
Messages are broadcast via WebSocket to:
```php
new PrivateChannel('sitePrivateChat.' . $conversation_id)
```

Event name: `siteBroadCast`

## Conversation Types

### 1-to-1 Conversations (TYPE_COUPLE)
- Requires `end_user_id` in message payload
- Prevents users from messaging themselves
- Messages tracked with sender and receiver

### Circle Conversations (TYPE_GROUP)
- No `end_user_id` required
- Messages read first-come-first-serve
- All circle members receive broadcasts

## Error Handling

### Backend Errors
- **400**: Missing end_user_id for 1-to-1 conversation
- **400**: Attempting to message yourself
- **500**: General server error

### Frontend Error Handling
```typescript
try {
  await sendMessage(messageData);
} catch (error) {
  // Remove optimistic message
  setMessages(prev => prev.filter(msg => !msg.id.startsWith('temp-')));
  // Show error to user
  alert('Failed to send message. Please try again.');
}
```

## Performance Considerations

### Message Limits
- Latest 30 messages loaded per conversation
- Pagination for older messages (to be implemented)

### Loading States
- Show loading indicator while fetching initial messages
- Disable send button while sending (`isSending` state)
- Show reduced opacity on send button during send

## Security

### Authentication
All chat endpoints require authentication via Sanctum middleware:
```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/post-chat', [ChatController::class, 'postChat']);
});
```

### Activity Tracking
The `TrackUserActivity` middleware logs user actions for analytics and monitoring.

### Validation
All inputs are validated and sanitized:
- Content max length: 5000 characters
- Conversation ID must exist
- User IDs must exist in users table

## Future Enhancements

- [ ] Message editing
- [ ] Message deletion
- [ ] Read receipts (utilizing seen_by fields)
- [ ] Message reactions
- [ ] File attachments (attachment field available)
- [ ] Message search
- [ ] Pagination for message history
- [ ] Typing indicators
- [ ] Push notifications for new messages
