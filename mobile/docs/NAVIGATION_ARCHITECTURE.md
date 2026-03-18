# Navigation Architecture & Auth Guard

## Problem Overview

### The Bug
When navigating from ChatList → ChatDetail and pressing the back button, the app would briefly show ChatList before jumping to the Home tab instead of staying on ChatList.

### Root Cause
The auth guard was implemented inside the nested `(app)/_layout.tsx` using conditional `<Redirect>` components. This caused:

1. **Navigation state re-evaluation** on every route change
2. **Stack history inconsistency** when the layout re-rendered
3. **Tab resets** to the initial route (Home) after back navigation

```tsx
// ❌ WRONG - Don't do this in nested layouts
const AppLayout = () => {
  const { session, isLoading } = useSession();
  
  if (!session) {
    return <Redirect href="/sign-in" />; // ⚠️ Causes navigation resets
  }
  
  return <Stack>...</Stack>;
};
```

## The Solution

### 1. Imperative Navigation Guard at Root Level

**File:** `app/_layout.tsx`

```tsx
function NavigationGuard() {
  const { session, isLoading } = useSession();
  const segments = useSegments();
  const router = useRouter();

  useEffect(() => {
    if (isLoading) return;

    const inApp = segments[0] === "(app)";

    // Only redirect when crossing auth boundaries
    if (!session && inApp) {
      router.replace("/sign-in");
    } else if (session && !inApp) {
      router.replace("/(app)/(tabs)/(home)");
    }
  }, [session, segments, isLoading]);

  return <StatusBar />;
}
```

**Why this works:**
- **Imperative navigation** (`router.replace()`) instead of declarative (`<Redirect>`)
- **Runs only at the root** - doesn't interfere with nested navigation
- **Only triggers on auth state changes** - not on every route change
- **Preserves navigation history** within authenticated sections

### 2. Clean Nested Layouts

**File:** `app/(app)/_layout.tsx`

```tsx
// ✅ CORRECT - Pure navigation, no auth logic
const AppLayout = () => {
  const colors = useThemeColours();

  return (
    <Stack screenOptions={{...}}>
      <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
    </Stack>
  );
};
```

### 3. Proper Back Navigation

**File:** `app/(app)/(tabs)/(chat)/[id].tsx`

```tsx
// ✅ CORRECT - Use dismiss() for stack navigation
<Pressable 
  onPress={() => {
    if (router.canDismiss()) {
      router.dismiss();
    } else {
      router.navigate('/(app)/(tabs)/(chat)');
    }
  }}
>
  <ChevronLeft />
</Pressable>
```

**Why `dismiss()` instead of `back()`:**
- `dismiss()` is designed for stack-based navigation
- Preserves tab state when popping from a stack
- More predictable than `back()` in nested navigators

## Architecture Principles

### File Structure
```
app/
  _layout.tsx              ← Auth guard (root level only)
  (auth)/
    sign-in.tsx
  (app)/
    _layout.tsx            ← Pure Stack navigation (no auth logic)
    (tabs)/
      _layout.tsx          ← Tab navigator
      (chat)/
        _layout.tsx        ← Chat stack
        index.tsx          ← ChatList
        [id].tsx           ← ChatDetail
```

### Navigation Guard Rules

✅ **DO:**
- Place auth guards at the **root layout** level
- Use **imperative navigation** (`router.replace()`, `router.navigate()`)
- Use `useSegments()` to detect route groups
- Guard only on **auth state changes**, not route changes
- Keep nested layouts **pure** (UI/navigation only)

❌ **DON'T:**
- Use `<Redirect>` in nested layouts
- Check auth state in multiple layout levels
- Use `initialRouteName` unless absolutely necessary
- Mix auth logic with navigation structure

### Navigation Method Guide

| Method | Use Case | Preserves History | Preserves Tab State |
|--------|----------|-------------------|---------------------|
| `router.push()` | Add to stack | ✅ | ✅ |
| `router.replace()` | Replace current | ❌ | ✅ |
| `router.back()` | Go back (generic) | ✅ | ⚠️ Can break tabs |
| `router.dismiss()` | Pop from stack | ✅ | ✅ |
| `router.navigate()` | Jump to route | Depends | ✅ |

## Testing Checklist

After implementing navigation changes, verify:

- [ ] ChatList → ChatDetail → Back = stays on ChatList
- [ ] Deep link from outside app works correctly
- [ ] Logging out redirects to sign-in
- [ ] Logging in redirects to home
- [ ] Tab switching preserves stack state
- [ ] Browser back button works (web)
- [ ] Android system back works

## Common Pitfalls

### 1. Auth Check in Multiple Layouts
```tsx
// ❌ Creates competing navigation logic
app/_layout.tsx         → checks auth
app/(app)/_layout.tsx   → also checks auth ⚠️
```

### 2. Using pathname Instead of segments
```tsx
// ❌ Brittle - breaks on route structure changes
const isApp = pathname.startsWith("/(app)");

// ✅ Better - uses route segment groups
const inApp = segments[0] === "(app)";
```

### 3. Synchronous Redirects in Render
```tsx
// ❌ Causes re-render loops
if (!session) {
  return <Redirect href="/sign-in" />;
}

// ✅ Async navigation in effect
useEffect(() => {
  if (!session) router.replace("/sign-in");
}, [session]);
```

## Related Issues

- **Expo Router Stack Navigation:** https://docs.expo.dev/router/advanced/stack/
- **Protected Routes Pattern:** https://docs.expo.dev/router/reference/authentication/
- **Navigation Methods:** https://docs.expo.dev/router/navigating-pages/

## Change History

- **2026-03-18:** Fixed back navigation bug by moving auth guard to root layout with imperative navigation
- **2026-03-18:** Removed `initialRouteName` from tabs to prevent resets
- **2026-03-18:** Changed chat detail back button to use `router.dismiss()`
