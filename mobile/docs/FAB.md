# FAB (Floating Action Button) Documentation

## Overview

The FAB component is a floating action button that appears on pages/tabs and expands to show multiple action options. It uses a global context to maintain state across all pages and provides smooth animations when expanding/collapsing.

## Features

- **Dynamic Actions**: Define custom actions per page or tab
- **Smooth Animations**: Staggered scale and translate animations when expanding
- **Global Context**: FAB state persists across navigation
- **Theme Support**: Automatically uses your app's theme colors
- **Backdrop Blur**: Semi-transparent backdrop when expanded
- **Icon Support**: Optional icons from lucide-react-native for each action

## File Structure

```
mobile/
├── components/
│   └── FAB.tsx              # Main FAB component
├── context/
│   └── FABContext.tsx       # FAB state management
├── app/
│   └── (app)/
│       └── (tabs)/
│           ├── _layout.tsx  # Wrapped with FABProvider
│           └── (home)/
│               └── home.tsx # Example usage
└── docs/
    └── FAB.md               # This file
```

## Setup

### 1. Wrap Your App with FABProvider

In your tabs layout (`app/(app)/(tabs)/_layout.tsx`):

```tsx
import { FABProvider } from '@/context/FABContext';

const TabsLayout = () => {
  return (
    <FABProvider>
      <ProfileOverlayProvider>
        <TabsLayoutContent />
      </ProfileOverlayProvider>
    </FABProvider>
  );
};
```

### 2. Use FAB in Your Pages

Import the FAB component and FABAction interface:

```tsx
import { FAB, FABAction } from '@/components/FAB';
import { Plus, Settings, Share2 } from 'lucide-react-native';
```

## API Reference

### FABAction Interface

```tsx
export interface FABAction {
  id: string;                           // Unique identifier for the action
  label: string;                        // Display text on the button
  icon?: React.ComponentType<any>;      // Optional icon component
  onPress: () => void;                  // Callback when action is pressed
}
```

### FAB Props

```tsx
interface FABProps {
  colors: any;                          // Theme colors object
  actions?: FABAction[];                // Array of action buttons
  onCoordinate?: () => void;            // Deprecated (use actions instead)
  onCreateCircle?: () => void;          // Deprecated (use actions instead)
}
```

## Usage Examples

### Basic Usage (Home Page)

```tsx
import { FAB, FABAction } from '@/components/FAB';
import { Plus } from 'lucide-react-native';

export const Home = ({ colors, onAddEvent, onCreateGroup }) => {
  const fabActions: FABAction[] = [
    {
      id: 'coordinate',
      label: 'Coordinate',
      onPress: () => onAddEvent?.(),
    },
    {
      id: 'create-circle',
      label: 'Create Circle',
      icon: Plus,
      onPress: () => onCreateGroup?.(),
    },
  ];

  return (
    <View style={{ flex: 1 }}>
      {/* Your content here */}
      <FAB colors={colors} actions={fabActions} />
    </View>
  );
};
```

### Calendar Page

```tsx
import { FAB, FABAction } from '@/components/FAB';
import { Calendar, Clock } from 'lucide-react-native';

const CalendarPage = ({ colors }) => {
  const fabActions: FABAction[] = [
    {
      id: 'create-event',
      label: 'New Event',
      icon: Calendar,
      onPress: () => handleCreateEvent(),
    },
    {
      id: 'set-reminder',
      label: 'Set Reminder',
      icon: Clock,
      onPress: () => handleSetReminder(),
    },
  ];

  return (
    <View style={{ flex: 1 }}>
      {/* Calendar content */}
      <FAB colors={colors} actions={fabActions} />
    </View>
  );
};
```

### Chat Page

```tsx
import { FAB, FABAction } from '@/components/FAB';
import { MessageSquare, Phone } from 'lucide-react-native';

const ChatPage = ({ colors }) => {
  const fabActions: FABAction[] = [
    {
      id: 'new-message',
      label: 'New Message',
      icon: MessageSquare,
      onPress: () => handleNewMessage(),
    },
    {
      id: 'start-call',
      label: 'Start Call',
      icon: Phone,
      onPress: () => handleStartCall(),
    },
  ];

  return (
    <View style={{ flex: 1 }}>
      {/* Chat content */}
      <FAB colors={colors} actions={fabActions} />
    </View>
  );
};
```

### Explore Page (No Actions)

```tsx
const ExplorePage = ({ colors }) => {
  // Don't show FAB or show with no actions
  return (
    <View style={{ flex: 1 }}>
      {/* Explore content */}
    </View>
  );
};
```

## Theming

The FAB component uses your app's theme colors:

- **Button Background**: `#171d0a` (fixed dark green)
- **Button Icon**: `#dae0e6` (light text)
- **Action Background**: `colors.card`
- **Action Border**: `colors.border`
- **Action Text**: `colors.text`
- **Action Icon**: `colors.accent`
- **Icon Color**: The accent color from your theme

## Animation Details

### Expand Animation
- **Duration**: 300ms total
- **Stagger**: 80ms delay between each button
- **Effect**: Scale (0.5 → 1) + Translate Up (-20px → 0px)
- **Fade**: 0 → 1 opacity

### Collapse Animation
- **Duration**: 200ms total
- **Effect**: Reverse of expand
- **No stagger** on collapse for quicker dismissal

### Backdrop
- **Opacity**: Fades in with first action
- **Background**: Semi-transparent black (0.3 opacity)
- **Tap to Close**: Closes FAB when backdrop is tapped

## Using FAB Context Directly

If you need to control FAB state programmatically:

```tsx
import { useFAB } from '@/context/FABContext';

const MyComponent = () => {
  const { isExpanded, toggleFAB, openFAB, closeFAB } = useFAB();

  return (
    <View>
      {/* Open FAB programmatically */}
      <Button onPress={openFAB} title="Open FAB" />
      
      {/* Close it */}
      <Button onPress={closeFAB} title="Close FAB" />
      
      {/* Toggle */}
      <Button onPress={toggleFAB} title="Toggle FAB" />
    </View>
  );
};
```

## Best Practices

1. **Keep Actions Minimal**: 2-4 actions per page for better UX
2. **Use Icons**: Include icons for better visual communication
3. **Clear Labels**: Use action names that clearly describe what happens
4. **Consistent Placement**: Always position FAB in bottom-right corner
5. **Page-Specific Actions**: Customize actions based on page context
6. **Avoid Overlaps**: Position FAB above or around other navigation/toolbars

## Common Patterns

### Coordinate & Create Actions (Home)

```tsx
{
  id: 'coordinate',
  label: 'Coordinate',
  onPress: () => navigation.navigate('CreateEvent'),
},
{
  id: 'create-circle',
  label: 'Create Circle',
  icon: Plus,
  onPress: () => navigation.navigate('CreateGroup'),
},
```

### Add & Search (Explore)

```tsx
{
  id: 'search',
  label: 'Search Spaces',
  icon: Search,
  onPress: () => openSearchModal(),
},
{
  id: 'create-space',
  label: 'Create Space',
  icon: Plus,
  onPress: () => openCreateSpaceModal(),
},
```

### Send & Call (Chat)

```tsx
{
  id: 'send-message',
  label: 'Send Message',
  icon: MessageSquare,
  onPress: () => openNewMessageModal(),
},
{
  id: 'video-call',
  label: 'Video Call',
  icon: VideoCall,
  onPress: () => initiateVideoCall(),
},
```

## Troubleshooting

### FAB Not Appearing
- Ensure FABProvider wraps your component tree
- Check that FAB component is rendered in your page
- Verify it's not hidden behind other elements (check z-index)

### Actions Not Working
- Verify callback functions are being passed correctly
- Check that `closeFAB()` is being called after action
- Ensure page context is available to callbacks

### Animations Not Smooth
- Ensure native driver is enabled (it is by default)
- Check for heavy computations in action callbacks
- Verify device performance

### Backdrop Not Showing
- Ensure `isExpanded` state is true
- Check z-index doesn't interfere
- Verify backdrop opacity value (defaults to 0.3)

## Performance Notes

- FAB uses `useNativeDriver` for all animations (60fps performance)
- Animated values are created once per unique action set
- Backdrop only renders when FAB is expanded
- Action buttons only render when expanded

## Future Enhancements

Potential improvements for future versions:
- Customizable animation speeds
- Custom FAB button styling
- Floating labels on hover
- Drag-to-reorder actions
- Keyboard shortcut support
- Haptic feedback on action press
