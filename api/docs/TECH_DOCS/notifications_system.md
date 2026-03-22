# Notifications System Documentation

## Overview

The THRD notifications system provides real-time notifications to users for various events including circle invitations, messages, calendar events, and system announcements. The system includes both API endpoints and WebSocket support for real-time delivery.

---

## Table of Contents

1. [Architecture](#architecture)
2. [Database Schema](#database-schema)
3. [API Endpoints](#api-endpoints)
4. [Enums](#enums)
5. [Frontend Components](#frontend-components)
6. [WebSocket Integration](#websocket-integration)
7. [Usage Examples](#usage-examples)

---

## Architecture

### Backend
- **Controller**: `api/app/Http/Controllers/Notifications/NotificationsController.php`
- **Model**: `api/app/Models/Notification.php`
- **Routes**: Defined in `api/routes/api.php` under "NOTIFICATION ROUTES"

### Frontend
- **Service**: `mobile/services/notificationService.ts`
- **Screens**:
  - List: `mobile/app/(app)/(notifications)/index.tsx`
  - Detail: `mobile/app/(app)/(notifications)/[id].tsx`
- **Components**: `mobile/components/notifications/NotificationItem.tsx`

---

## Database Schema

### Table: `notifications`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigInteger | Primary key |
| `user_id` | bigInteger | Recipient user ID |
| `from_id` | bigInteger | Sender user ID (nullable) |
| `fk_circle_item_post_id` | string | Related circle ID (nullable) |
| `fk_conversation_id` | string | Related conversation ID (nullable) |
| `type` | string | Notification type (see enums) |
| `title` | string(2000) | Notification title |
| `comment` | longText | Notification message body |
| `status` | string(50) | Read status: 'unread' or 'read' |
| `color_status` | string(50) | Display color for notification |
| `created_at` | dateTime | Creation timestamp |
| `updated_at` | dateTime | Last update timestamp |

### Indexes
- Primary key on `id`
- Index on `user_id` for fast user queries
- Index on `status` for filtering

---

## API Endpoints

### 1. Get Notifications
**Endpoint**: `POST /api/notifications`

**Authentication**: Required (Sanctum)

**Request Body**:
```json
{
  "type": "circle_request",  // Optional: filter by type
  "status": "unread",        // Optional: filter by status
  "limit": 30,               // Optional: default 30, max 100
  "offset": 0                // Optional: for pagination
}
```

**Response**:
```json
{
  "success": true,
  "notifications": [
    {
      "id": 123,
      "user_id": 85,
      "from_id": 42,
      "type": "circle_request",
      "title": "You received an invite from Tech Circle",
      "comment": "You have received an invite to join...",
      "status": "unread",
      "color_status": "#8B7355",
      "created_at": "2026-03-22T10:30:00Z",
      "updated_at": "2026-03-22T10:30:00Z",
      "from_user": {
        "id": 42,
        "name": "John Doe",
        "firstname": "John",
        "username": "johndoe"
      }
    }
  ],
  "hasMore": true
}
```

---

### 2. Get Notification by ID
**Endpoint**: `POST /api/notification`

**Authentication**: Required (Sanctum)

**Request Body**:
```json
{
  "notification_id": 123
}
```

**Response**:
```json
{
  "success": true,
  "notification": {
    "id": 123,
    "user_id": 85,
    "from_id": 42,
    "type": "circle_request",
    "title": "You received an invite from Tech Circle",
    "comment": "You have received an invite to join...",
    "status": "read",
    "created_at": "2026-03-22T10:30:00Z",
    "from_user": { ... },
    "circle_request": {
      "id": 456,
      "circle_id": 789,
      "circle_name": "Tech Circle",
      "requester_id": 42,
      "requester_name": "John Doe",
      "status": "pending",
      "created_at": "2026-03-22T10:30:00Z"
    }
  }
}
```

**Notes**:
- Automatically includes `circle_request` details if type is 'circle_request'
- Loads sender information from `from_id`

---

### 3. Mark Notification as Read
**Endpoint**: `POST /api/notification/mark-read`

**Authentication**: Required (Sanctum)

**Request Body**:
```json
{
  "notification_id": 123
}
```

**Response**:
```json
{
  "success": true,
  "message": "Notification marked as read."
}
```

---

### 4. Get Unread Count
**Endpoint**: `GET /api/notifications/unread-count`

**Authentication**: Required (Sanctum)

**Query Parameters**:
- `type` (optional): Filter by notification type

**Response**:
```json
{
  "success": true,
  "unread_count": 5
}
```

---

## Enums

### Notification Types
```typescript
enum NotificationType {
  CIRCLE_REQUEST = 'circle_request',  // Circle invitation
  MESSAGE = 'message',                // New message
  CALENDAR = 'calendar',              // Calendar event
  SYSTEM = 'system',                  // System announcement
}
```

### Notification Status
```typescript
enum NotificationStatus {
  UNREAD = 'unread',  // Not yet viewed
  READ = 'read',      // User has viewed
}
```

### Database Status Values
- **Notification Status**: `'unread'`, `'read'`
- **Circle Request Status**: `'pending'`, `'accepted'`, `'declined'`

---

## Frontend Components

### 1. Notifications List Screen
**Location**: `mobile/app/(app)/(notifications)/index.tsx`

**Features**:
- Filter tabs: ALL, INVITES, MESSAGES, CIRCLES, CALENDAR
- Pull-to-refresh
- Infinite scroll pagination
- Real-time WebSocket notifications
- Unread indicators

**Usage**:
```typescript
// Navigation
router.push('/(app)/(notifications)');
```

---

### 2. Notification Detail Screen
**Location**: `mobile/app/(app)/(notifications)/[id].tsx`

**Features**:
- Full notification details
- Circle invitation actions (Accept/Decline)
- Auto-mark as read when viewed
- Circle request details if applicable

**Usage**:
```typescript
// Navigation
router.push(`/(app)/(notifications)/${notificationId}`);
```

---

### 3. NotificationItem Component
**Location**: `mobile/components/notifications/NotificationItem.tsx`

**Props**:
```typescript
interface NotificationItemProps {
  notification: Notification;
  onPress: () => void;
}
```

**Features**:
- User avatar with initials
- Unread indicator dot
- Timestamp (relative)
- Notification preview

---

## WebSocket Integration

### Backend Setup (Soketi)
The backend uses Soketi (Laravel WebSockets alternative) for real-time notifications.

**Configuration**: `api/soketi.config.json`

**Broadcasting**: Notifications are broadcast to:
```
private-notifications.{userId}
```

**Event Name**:
```
notification.new
```

---

### Frontend WebSocket Manager

**Location**: `mobile/services/notificationService.ts`

**Initialization**:
```typescript
import { notificationWebSocket } from '@/services/notificationService';

// Initialize connection
notificationWebSocket.initialize();

// Subscribe to notifications
const unsubscribe = notificationWebSocket.onNotification((notification) => {
  console.log('New notification:', notification);
  // Handle notification
});

// Cleanup
unsubscribe();
notificationWebSocket.disconnect();
```

**Features**:
- Auto-reconnect on connection loss
- Vibration on new notification
- Event-driven architecture
- Multiple listener support

---

## Usage Examples

### Creating a Notification (Backend)

```php
use App\Models\Notification;

// Create circle invitation notification
Notification::create([
    'user_id' => $invitedUserId,
    'from_id' => $currentUserId,
    'fk_circle_item_post_id' => $circleId,
    'type' => 'circle_request',
    'title' => "You received an invite from {$circleName}",
    'comment' => "You have received an invite to join the circle \"{$circleName}\".",
    'status' => 'unread',
    'color_status' => Notification::getRandomColor(),
]);

// Broadcast via WebSocket
broadcast(new NotificationCreated($notification));
```

---

### Fetching Notifications (Frontend)

```typescript
import { getNotifications, NotificationType } from '@/services/notificationService';

// Get all notifications
const { notifications, hasMore } = await getNotifications();

// Get only circle invitations
const invites = await getNotifications({
  type: NotificationType.CIRCLE_REQUEST,
  limit: 20,
  offset: 0,
});

// Get unread notifications
const unread = await getNotifications({
  status: NotificationStatus.UNREAD,
});
```

---

### Handling Circle Invitations

```typescript
import {
  getNotificationById,
  markNotificationAsRead,
} from '@/services/notificationService';
import { acceptCircleInvite, denyCircleInvite } from '@/services/chatService';

// Load notification
const notification = await getNotificationById(123);

// Mark as read
await markNotificationAsRead(123);

// Accept invitation
if (notification.circle_request) {
  await acceptCircleInvite(notification.circle_request.id);
}

// Decline invitation
if (notification.circle_request) {
  await denyCircleInvite(notification.circle_request.id);
}
```

---

### Phone Vibration

```typescript
import { vibrateForNotification, NotificationType } from '@/services/notificationService';

// Vibrate for circle invitation (double vibration)
await vibrateForNotification(NotificationType.CIRCLE_REQUEST);

// Vibrate for message (single vibration)
await vibrateForNotification(NotificationType.MESSAGE);
```

---

## Best Practices

1. **Always mark notifications as read** when the user views them
2. **Use WebSocket for real-time updates** instead of polling
3. **Filter notifications on backend** for better performance
4. **Implement pagination** to avoid loading too many notifications at once
5. **Show loading states** during API calls
6. **Handle errors gracefully** with user-friendly messages
7. **Vibrate on notification** for better UX
8. **Use unread count** to show notification badge

---

## Security Considerations

1. **User Authorization**: All endpoints verify `user_id` matches authenticated user
2. **WebSocket Authentication**: Private channels require authentication
3. **Input Validation**: All requests are validated before processing
4. **XSS Prevention**: Notification content should be sanitized before display
5. **Rate Limiting**: Consider implementing rate limits on notification endpoints

---

## Future Enhancements

- [ ] Push notifications (Firebase/APNs)
- [ ] Notification preferences/settings
- [ ] Bulk mark as read
- [ ] Notification sounds
- [ ] Email notifications
- [ ] In-app notification banner
- [ ] Notification grouping by type
- [ ] Archive notifications

---

## Related Documentation

- [Circle Invitations System](circles_and_conversations.md)
- [WebSocket Broadcasting](../NOTES/websocket-setup.md)
- [Chat System](circles_and_conversations.md)

---

**Last Updated**: March 22, 2026
**Version**: 1.0.0
