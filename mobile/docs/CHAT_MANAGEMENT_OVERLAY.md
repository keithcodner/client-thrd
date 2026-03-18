# Chat Management Overlay

## Overview

The `ChatManagementOverlay` is a slide-down modal component that provides quick actions and management options for chat items. It appears when a user long-presses on a chat item in the chat list.

## Features

### Quick Actions (Header Bar)
The quick action buttons are dynamically rendered from a configurable array:
- **Delete Button**: Removes the chat from the user's list
- **Pin Button**: Pins the chat to the top of the list
- **More Options (3 dots)**: Toggles dropdown menu with additional actions

### Adding New Quick Action Buttons
To add a new quick action button, add an item to the `quickActionButtons` array:

```tsx
{
  id: 'share',
  icon: Share2,
  color: colours.primary,
  onPress: handleShare,
}
```

### Dropdown Menu Options
The dropdown menu is dynamically rendered from a configurable array of menu items:
- **View Info**: Navigate to the circle's detail/info page
- **Leave Circle**: Leave the circle (removes from your list)
- **Delete Circle**: Delete the entire circle (owner only)
- **Clear Chats**: Clear all messages in the chat

### Adding New Menu Options
To add a new dropdown menu option, add an item to the `dropdownMenuItems` array:

```tsx
{
  id: 'mute-notifications',
  label: 'Mute Notifications',
  icon: BellOff,
  color: colours.secondaryText,
  onPress: handleMuteNotifications,
  showOnlyForOwner: false, // Optional, defaults to showing for everyone
}
```

## Component Structure

```
ChatManagementOverlay/
├── Modal Backdrop (dismissible)
├── Animated Overlay Container
│   ├── Header (Quick Actions)
│   │   ├── Back Button
│   │   ├── Chat Name
│   │   └── Action Buttons (dynamically rendered from quickActionButtons array)
│   └── Dropdown Menu (conditional, dynamically rendered)
│       └── Menu Items (from dropdownMenuItems array)
```

## Quick Action Buttons Configuration

### QuickActionButton Interface

```tsx
interface QuickActionButton {
  id: string;              // Unique identifier for the button
  icon: LucideIcon;        // Icon component from lucide-react-native
  color: string;           // Icon color
  onPress: () => void;     // Click handler
  specialStyle?: boolean;  // For custom background behavior (e.g., "more" button)
}
```

### Current Quick Action Buttons

```tsx
const quickActionButtons: QuickActionButton[] = [
  {
    id: 'delete',
    icon: Trash2,
    color: colours.error,
    onPress: handleDeleteChat,
  },
  {
    id: 'pin',
    icon: Pin,
    color: colours.warning,
    onPress: handlePinChat,
  },
  {
    id: 'more',
    icon: MoreVertical,
    color: showDropdown ? '#fff' : colours.text,
    onPress: toggleDropdown,
    specialStyle: true, // Changes background when dropdown is open
  },
];
```

### Adding New Quick Action Buttons

1. **Import the icon** (if not already imported):
   ```tsx
   import { Share2 } from 'lucide-react-native';
   ```

2. **Create the handler function**:
   ```tsx
   const handleShare = () => {
     if (chat) {
       // Share logic here
     }
   };
   ```

3. **Add to the array**:
   ```tsx
   {
     id: 'share',
     icon: Share2,
     color: colours.primary,
     onPress: handleShare,
   }
   ```

The button will automatically:
- Render with consistent styling
- Apply proper sizing (40x40 rounded circle)
- Handle press states
- Maintain proper spacing in the row

## Dropdown Menu Configuration

### DropdownMenuItem Interface

```tsx
interface DropdownMenuItem {
  id: string;                // Unique identifier for the menu item
  label: string;             // Display text
  icon: LucideIcon;          // Icon component from lucide-react-native
  color?: string;            // Icon/text color (optional)
  onPress: () => void;       // Click handler
  showOnlyForOwner?: boolean; // Show only if user is circle owner (optional)
}
```

### Current Menu Items

```tsx
const dropdownMenuItems: DropdownMenuItem[] = [
  {
    id: 'view-info',
    label: 'View Info',
    icon: Info,
    color: colours.info,
    onPress: handleViewInfo,
  },
  {
    id: 'leave-circle',
    label: 'Leave Circle',
    icon: LogOut,
    color: colours.warning,
    onPress: handleLeaveCircle,
  },
  {
    id: 'delete-circle',
    label: 'Delete Circle',
    icon: Trash2,
    color: colours.error,
    onPress: handleDeleteCircle,
    showOnlyForOwner: true, // Only visible to circle owners
  },
  {
    id: 'clear-chats',
    label: 'Clear Chats',
    icon: MessageSquare,
    color: colours.secondaryText,
    onPress: handleClearChats,
  },
];
```

### Adding New Menu Items

1. **Import the icon** (if not already imported):
   ```tsx
   import { BellOff } from 'lucide-react-native';
   ```

2. **Create the handler function**:
   ```tsx
   const handleMuteNotifications = () => {
     if (chat) {
       handleClose();
       // Your logic here
     }
   };
   ```

3. **Add to the array**:
   ```tsx
   {
     id: 'mute-notifications',
     label: 'Mute Notifications',
     icon: BellOff,
     color: colours.secondaryText,
     onPress: handleMuteNotifications,
   }
   ```

The menu will automatically:
- Render the new item in the correct position
- Apply proper styling and borders
- Handle press states
- Filter based on `showOnlyForOwner` if specified

## Usage

### Basic Implementation

```tsx
import { ChatManagementOverlay } from '@/components/chat/ChatManagementOverlay';
import { ChatItemData } from '@/components/chat/ChatListItem';

const [selectedChat, setSelectedChat] = useState<ChatItemData | null>(null);
const [showOverlay, setShowOverlay] = useState(false);

// In your JSX:
<ChatManagementOverlay
  visible={showOverlay}
  chat={selectedChat}
  onClose={() => setShowOverlay(false)}
  onDelete={(chatId) => handleDelete(chatId)}
  onPin={(chatId) => handlePin(chatId)}
  onLeave={(chatId) => handleLeave(chatId)}
  onClearChats={(chatId) => handleClear(chatId)}
  isOwner={true} // Show/hide "Delete Circle" option
/>
```

### Integration with ChatListItem

```tsx
<ChatListItem
  chat={chat}
  onLongPress={(chat) => {
    setSelectedChat(chat);
    setShowOverlay(true);
  }}
/>
```

## Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `visible` | `boolean` | Yes | Controls overlay visibility |
| `chat` | `ChatItemData \| null` | Yes | Chat data to display |
| `onClose` | `() => void` | Yes | Called when overlay is dismissed |
| `onDelete` | `(chatId: string) => void` | No | Handle chat deletion |
| `onPin` | `(chatId: string) => void` | No | Handle chat pinning |
| `onLeave` | `(chatId: string) => void` | No | Handle leaving circle |
| `onClearChats` | `(chatId: string) => void` | No | Handle clearing messages |
| `isOwner` | `boolean` | No | Shows "Delete Circle" option (default: false) |

## Animation Details

### Slide-In Animation
- **Duration**: Spring animation with tension: 65, friction: 11
- **Direction**: Slides down from top
- **Initial Position**: -240px (overlay height)
- **Final Position**: 0px

### Slide-Out Animation
- **Duration**: 200ms
- **Easing**: Timing animation
- **Direction**: Slides up to hide

## Navigation Considerations

### Parent Navigation State
The overlay is designed to work within the chat tab navigation structure:

```
(app)/
  (tabs)/
    (chat)/
      index.tsx          ← ChatManagementOverlay used here
      [id].tsx           ← Can navigate here via "View Info"
```

### Navigation Actions

1. **View Info**: Uses `router.push()` to navigate to chat detail
   ```tsx
   router.push(`/(app)/(tabs)/(chat)/${chat.id}`);
   ```

2. **Close Overlay**: Automatically closes before any navigation

3. **State Management**: Selected chat state is cleared after animation completes

### Important Navigation Pattern

```tsx
const handleViewInfo = () => {
  if (chat) {
    handleClose(); // Close overlay first
    router.push(`/(app)/(tabs)/(chat)/${chat.id}`); // Then navigate
  }
};
```

**Why this matters:**
- Prevents modal state conflicts during navigation
- Ensures clean navigation history
- Avoids overlay appearing on subsequent screens

## Styling & Theming

The component uses the `useThemeColours` hook for consistent theming:

- **Background**: `colours.background`
- **Card/Surface**: `colours.card`, `colours.surface`
- **Text**: `colours.text`, `colours.secondaryText`
- **Actions**: `colours.error` (delete), `colours.warning` (leave/pin), `colours.info` (info)

### Shadow Configuration

```tsx
shadowColor: '#000',
shadowOffset: { width: 0, height: 4 },
shadowOpacity: 0.3,
shadowRadius: 8,
```

## Action Handlers

### Delete Chat
Removes the chat from the user's chat list (local UI only).

```tsx
const handleDeleteChat = (chatId: string) => {
  setChats(chats.filter(chat => chat.id !== chatId));
  Toast.show({
    type: 'success',
    text1: 'Deleted',
    text2: 'Chat removed from your list.',
  });
};
```

### Pin Chat
Pins the chat to the top of the list (to be implemented).

```tsx
const handlePinChat = (chatId: string) => {
  Toast.show({
    type: 'success',
    text1: 'Pinned',
    text2: 'Chat pinned to top.',
  });
  // TODO: Implement pin functionality
};
```

### Leave Circle
User leaves the circle (requires API call).

```tsx
const handleLeaveCircle = (chatId: string) => {
  // API call: await leaveCircle(chatId);
  handleDeleteChat(chatId); // Remove from UI
  Toast.show({
    type: 'info',
    text1: 'Left Circle',
    text2: 'You have left the circle.',
  });
};
```

### Clear Chats
Clears all messages in the chat (requires API call).

```tsx
const handleClearChats = (chatId: string) => {
  // API call: await clearMessages(chatId);
  Toast.show({
    type: 'success',
    text1: 'Cleared',
    text2: 'All messages have been cleared.',
  });
};
```

### Delete Circle (Owner Only)
Permanently deletes the entire circle.

```tsx
const handleDeleteCircle = (chatId: string) => {
  // API call: await deleteCircle(chatId);
  handleDeleteChat(chatId); // Remove from UI
  Toast.show({
    type: 'success',
    text1: 'Deleted',
    text2: 'Circle has been deleted.',
  });
};
```

## State Management

### Overlay State Pattern

```tsx
const [selectedChat, setSelectedChat] = useState<ChatItemData | null>(null);
const [showManagementOverlay, setShowManagementOverlay] = useState(false);

const handleChatLongPress = (chat: ChatItemData) => {
  setSelectedChat(chat);
  setShowManagementOverlay(true);
};

const handleCloseOverlay = () => {
  setShowManagementOverlay(false);
  // Clear selected chat after animation completes
  setTimeout(() => setSelectedChat(null), 300);
};
```

**Why the timeout?**
- Allows slide-out animation to complete before clearing data
- Prevents visual glitches during transition
- 300ms matches the animation duration

## User Experience

### Interaction Flow

1. **Long Press** on chat item
2. **Overlay slides down** from top
3. **Quick actions** immediately visible
4. **Tap 3 dots** to see more options
5. **Select action** → overlay closes → action executes

### Feedback Mechanisms

- **Haptic Feedback**: Long press triggers native vibration (platform-specific)
- **Visual Feedback**: Pressed states on all buttons
- **Toast Notifications**: Confirms action completion
- **Animation**: Smooth transitions for professional feel

## Accessibility

### Pressable Components
All interactive elements use `Pressable` with proper touch targets (minimum 44x44 points).

### Color Contrast
Action buttons use appropriate colors:
- **Destructive actions**: Red (`colours.error`)
- **Warning actions**: Orange (`colours.warning`)
- **Informational**: Blue (`colours.info`)
- **Neutral**: Theme text color

## Performance Considerations

### Animation Optimization
- Uses `useNativeDriver: true` for optimal performance
- Hardware-accelerated transforms
- Minimal re-renders through proper state management

### Conditional Rendering
- Dropdown menu only renders when open
- Component returns `null` if no chat selected
- Modal backdrop handled by React Native Modal component

## Future Enhancements

### Planned Features
With the new configuration system for both quick actions and dropdown menu, adding these features is straightforward:

**Quick Action Buttons:**
- [ ] Share button - Add as:
  ```tsx
  { id: 'share', icon: Share2, color: colours.primary, onPress: handleShare }
  ```
- [ ] Star/Favorite button
- [ ] Notification toggle

**Dropdown Menu Items:**
- [ ] Batch selection mode (select multiple chats)
- [ ] Custom pin colors/labels
- [ ] Archive functionality - Just add to `dropdownMenuItems`:
  ```tsx
  { id: 'archive', label: 'Archive', icon: Archive, onPress: handleArchive }
  ```
- [ ] Mute notifications toggle - Add as:
  ```tsx
  { id: 'mute', label: 'Mute', icon: BellOff, onPress: handleMute }
  ```
- [ ] Export chat history
- [ ] Share circle invite link
- [ ] Report circle (non-owners only):
  ```tsx
  { 
    id: 'report', 
    label: 'Report', 
    icon: Flag, 
    color: colours.error,
    onPress: handleReport,
    showOnlyForOwner: false // Could add showOnlyForNonOwner logic
  }
  ```

### API Integration TODOs
- [ ] Connect `onLeave` to leave circle endpoint
- [ ] Connect `onClearChats` to clear messages endpoint
- [ ] Connect `onPin` to update user preferences
- [ ] Implement owner check via API (currently hardcoded)
- [ ] Add confirmation dialogs for destructive actions

## Testing Checklist

- [ ] Long press triggers overlay
- [ ] Overlay slides in smoothly
- [ ] Back button closes overlay
- [ ] Delete removes chat from list
- [ ] Pin shows success toast
- [ ] 3 dots toggles dropdown
- [ ] All dropdown options are tappable
- [ ] "Delete Circle" only shows for owners
- [ ] View Info navigates correctly
- [ ] Overlay closes before navigation
- [ ] Backdrop dismisses overlay
- [ ] Animation completes before state clear
- [ ] Works with different theme modes

## Common Issues & Solutions

### Overlay Not Showing
**Issue**: Long press doesn't trigger overlay  
**Solution**: Ensure `onLongPress` prop is passed to `ChatListItem`

### Navigation Issues
**Issue**: Overlay appears on next screen  
**Solution**: Always call `handleClose()` before navigation

### Animation Jank
**Issue**: Overlay animation stutters  
**Solution**: Ensure `useNativeDriver: true` is set

### Missing Dropdown Options
**Issue**: "Delete Circle" not visible  
**Solution**: Check `isOwner` prop is set correctly

## Related Components

- [`ChatListItem`](../components/chat/ChatListItem.tsx) - Individual chat item with long press support
- [`CircleInfoModal`](../components/chat/CircleInfoModal.tsx) - Detailed circle information view
- [`CreateCircleModal`](../components/app/CreateCircleModal.tsx) - Circle creation flow

## Related Documentation

- [Navigation Architecture](./NAVIGATION_ARCHITECTURE.md) - Understanding app navigation patterns
- [FAB Component](./FAB.md) - Floating action button documentation

## Change History

- **2026-03-18**: Initial implementation with slide-down overlay and dropdown menu
- **2026-03-18**: Added navigation handling and state management patterns
- **2026-03-18**: Integrated with chat index page and toast notifications
