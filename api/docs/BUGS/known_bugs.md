# Known Bugs and Todos

## Bugs

- **Timezone Fix**: Ensure that all timestamps displayed in the application are consistent with the user's local timezone. This includes messages, notifications, and any time-sensitive data. Investigate and resolve any discrepancies caused by server-side timezone settings.

- **Offline/Online Notifications**: ~~Address the issue where user status (online/offline) does not update in real-time.~~ **RESOLVED (2026-03-25)** — Presence channel was returning 403 due to three compounding issues: (1) the channel callback in `channels.php` was named `presence-conversation.{id}` but Laravel strips the `presence-` prefix before matching, so the correct name is `conversation.{id}`; (2) `Broadcast::routes()` was called twice (in `BroadcastServiceProvider` and `api.php`), causing the second call to override channel definitions; (3) `websocketService.ts` was initialized with a stale/undefined token before the session was ready. All three issues fixed. See `mobile/docs/WEBSOCKETS_REALTIME_MESSAGING.md` Issue 7 for full details.

- **Notification Badge and Message Indicator**: The notification badge, new message indicator (green dot), and the circle around the latest message are inconsistent. These currently rely on timers and polling, which can lead to delays and inaccuracies. Transition to a WebSocket-based system for real-time updates to improve reliability and user experience.

- **User Permissions Validation**: Implement robust user permission checks for all actions that modify data (CRUD operations). This will ensure that only authorized users can perform sensitive operations. Note that this does not apply to read-only actions like fetching message counts.

## Todos

- **Additional Notification Types**: Expand the notification system to include other types of notifications, such as reminders, event updates, and system alerts. Define the structure and delivery mechanism for these new notification types.

- **Events/Calendar**: Begin development of the events and calendar feature. This should include the ability to create, edit, and view events, as well as integration with notifications for event reminders.

- **Explore Feature**: Start work on the "Explore" section of the application. Define its purpose and functionality, such as discovering new content, users, or groups.

- **Idea Boards**: Develop the idea boards feature, allowing users to brainstorm, share, and collaborate on ideas. Include functionality for creating, editing, and organizing ideas.

- **Account Settings**: Begin work on the account settings section. This should include options for updating user information, changing passwords, managing privacy settings, and configuring notification preferences.