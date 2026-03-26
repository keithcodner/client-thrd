# Calendar System — Mobile Documentation

## Overview

The THRD calendar allows users to view their schedule in Month, Week, Day, and List views. Users can add and edit personal time blocks (events) from the Day view. Events created here sync across all views.

---

## Table of Contents

1. [File Structure](#file-structure)
2. [Navigation & Routing](#navigation--routing)
3. [Views](#views)
   - [Month View](#month-view)
   - [Week View](#week-view)
   - [Day View](#day-view)
4. [Modals](#modals)
   - [CreateTimeBlock](#createtimeblock)
   - [EditTimeBlock](#edittimeblock)
5. [Components Reference](#components-reference)
6. [Service Layer](#service-layer)
7. [Data Flow](#data-flow)

---

## File Structure

```
mobile/
├── app/(app)/(tabs)/(calendar)/
│   ├── _layout.tsx          # Stack navigator, headerShown: false
│   ├── index.tsx            # Default → renders MonthView
│   ├── month.tsx            # Explicit month route → renders MonthView
│   ├── week.tsx             # Week view screen
│   ├── day.tsx              # Day view screen (accepts ?date= param)
│   ├── list.tsx             # List view screen (stub)
│   └── [id].tsx             # Event detail (future use)
│
├── components/calendar/
│   ├── month/
│   │   ├── MonthView.tsx       # Composite month screen; fetches events
│   │   ├── CalendarHeader.tsx  # "March / 2026 / ✕"
│   │   ├── ViewTabs.tsx        # MONTH | WEEK | DAY | LIST tab bar
│   │   ├── CalendarGrid.tsx    # 6-week grid
│   │   └── DayCell.tsx         # Single day cell (today circle, event dots)
│   │
│   ├── week/
│   │   ├── WeekDay.tsx             # Day card (day name, date, event list)
│   │   ├── WeekListItem.tsx        # Single event row inside WeekDay
│   │   ├── SuggestedOverlaps.tsx   # "Suggested Overlaps" section
│   │   └── SuggestedOverlapCard.tsx # Single overlap slot card
│   │
│   ├── day/
│   │   ├── DayHeader.tsx        # "Thursday 26  WEEKLY VIEW"
│   │   ├── DayTimeline.tsx      # 24-hour scrollable grid
│   │   ├── DayTimeBlock.tsx     # Absolutely-positioned event block
│   │   └── TimePickerModal.tsx  # Reusable hour/minute/AM-PM picker
│   │
│   ├── CreateTimeBlock.tsx   # "Add Time Block" bottom sheet
│   └── EditTimeBlock.tsx     # "Edit Time Block" bottom sheet
│
└── services/
    └── calendarService.ts    # All API calls + helper types
```

---

## Navigation & Routing

The calendar uses expo-router file-based routing. All views live under `(calendar)/` and share the same `_layout.tsx` Stack.

| Route                          | Screen        |
|--------------------------------|---------------|
| `/(app)/(tabs)/(calendar)/`    | Month view    |
| `/(app)/(tabs)/(calendar)/week`| Week view     |
| `/(app)/(tabs)/(calendar)/day` | Day view      |
| `/(app)/(tabs)/(calendar)/list`| List view     |

### Passing a date to the Day view

When navigating to the day view from week or month, pass the date as a query param:

```ts
router.push({
  pathname: '/(app)/(tabs)/(calendar)/day',
  params: { date: '2026-03-26' },
});
```

Inside `day.tsx`, the date is read via:
```ts
const params = useLocalSearchParams<{ date?: string }>();
const selectedDate = params.date ? new Date(params.date) : new Date();
```

---

## Views

### Month View

**Screen**: `index.tsx` / `month.tsx` → `MonthView.tsx`

- Renders a full 6-week calendar grid for the current month.
- Loads events from `fetchMonthEvents` on mount and displays colored dots on days that have events.
- Tapping any day navigates to that day's **Day View**.
- The WEEK / DAY / LIST tabs navigate to the respective screens.

**Key props (MonthView):**

| Prop         | Type       | Description                        |
|--------------|------------|------------------------------------|
| `onDayPress` | function   | Optional callback after navigation |
| `onAddEvent` | function   | Called when FAB is pressed         |

---

### Week View

**Screen**: `week.tsx`

- Shows the current Sunday–Saturday week.
- Loads events from `fetchMonthEvents` and maps them into per-day lists.
- The **Suggested Overlaps** section at the top uses dummy data — replace with real endpoint when available.
- Tapping any `WeekDay` card navigates to its **Day View**.

---

### Day View

**Screen**: `day.tsx` (accepts `?date=YYYY-MM-DD`)

- Displays a 24-hour scrollable timeline.
- Scrolls to 8 AM on initial load.
- Loads events for the selected day from `fetchMonthEvents` (filtered client-side by date).
- Tapping an event block opens **EditTimeBlock**.
- FAB `+` button opens **CreateTimeBlock**.
- "WEEKLY VIEW" link navigates back to the Week view.

---

## Modals

### CreateTimeBlock

`mobile/components/calendar/CreateTimeBlock.tsx`

Bottom sheet for adding a new time block.

**Props:**

| Prop           | Type                                                                              | Description                              |
|----------------|-----------------------------------------------------------------------------------|------------------------------------------|
| `visible`      | `boolean`                                                                         | Controls modal visibility                |
| `selectedDate` | `Date`                                                                            | The day the event will be added to       |
| `onClose`      | `() => void`                                                                      | Called when user closes the modal        |
| `onSave`       | `(payload: { name, start_at, end_at, color }) => Promise<void>` | Called when user taps "Save Block"       |

**Fields:**
- **Label** — text input (e.g. REST, STUDIO, WORK, Focus Time)
- **Starts** — time picker (taps open `TimePickerModal`)
- **Ends** — time picker

---

### EditTimeBlock

`mobile/components/calendar/EditTimeBlock.tsx`

Bottom sheet pre-populated with an existing event's data.

**Props:**

| Prop       | Type                                                                                         | Description                           |
|------------|----------------------------------------------------------------------------------------------|---------------------------------------|
| `visible`  | `boolean`                                                                                    | Controls modal visibility             |
| `event`    | `DayEvent \| null`                                                                           | The event being edited                |
| `onClose`  | `() => void`                                                                                 |                                       |
| `onSave`   | `(id: string, payload: { name, start_at, end_at, color }) => Promise<void>` | Save changes                          |
| `onDelete` | `(id: string) => Promise<void>`                                                              | Delete the event                      |

---

## Components Reference

### `DayTimeBlock`

Absolutely positioned event rectangle on the timeline.

```ts
interface DayEvent {
  id: string;
  name: string;
  start_at: string; // ISO
  end_at: string;   // ISO
  color: string;    // hex
}
```

Position is calculated as:
```
top    = (startHour - dayStartHour + startMin/60) × HOUR_HEIGHT
height = durationHours × HOUR_HEIGHT
```

### `TimePickerModal`

Reusable modal with three columns: **Hours (1–12)**, **Minutes (00/15/30/45)**, **AM/PM**.

```ts
interface TimeValue {
  hour: number;       // 1–12
  minute: 0|15|30|45;
  period: 'AM'|'PM';
}
```

Helper functions exported from `TimePickerModal.tsx`:
- `formatTimeValue(tv)` → `"9:00 AM"`
- `timeValueToDate(base, tv)` → `Date` (applies time to a date)
- `dateToTimeValue(date)` → `TimeValue`

### `DayCell` (month grid)

```ts
interface CalendarEvent {
  id: string;
  color: string; // hex dot color
  date: string;  // "YYYY-MM-DD"
}
```

---

## Service Layer

`mobile/services/calendarService.ts`

### Types

```ts
interface CalendarEventPayload {
  name: string;
  start_at: string; // ISO 8601
  end_at: string;
  color?: string;
  description?: string;
}

interface CalendarEventResponse {
  id: string;
  user_from_id: number;
  event_group_id: number | null;
  name: string;
  start_at: string;  // normalised alias
  end_at: string;    // normalised alias
  color: string;
  // ...
}
```

### Functions

| Function                                 | Description                              |
|------------------------------------------|------------------------------------------|
| `fetchMonthEvents(year, month)`          | GET /calendar/events?year=&month=        |
| `fetchEvent(id)`                         | GET /calendar/events/:id                 |
| `createCalendarEvent(payload)`           | POST /calendar/events                    |
| `updateCalendarEvent(id, payload)`       | PUT /calendar/events/:id                 |
| `deleteCalendarEvent(id)`                | DELETE /calendar/events/:id              |

All functions automatically normalise raw API rows so `start_at` / `end_at` / `color` are always present.

---

## Data Flow

```
User taps FAB (+)
  → CreateTimeBlock modal opens
  → User fills label + start/end time
  → onSave() calls createCalendarEvent()
    → POST /calendar/events
      → EventController::store()
        → Creates EventGroup (group_name = label)
        → Creates Event (event_group_id = group.id)
        → Updates EventGroup (event_id = event.id)
    ← Returns new event
  → loadEvents() refreshes the timeline
  → DayTimeline re-renders with new DayTimeBlock
```

Events are also visible in:
- **Month view** — as colored dots on the day cell (loaded via `fetchMonthEvents`)
- **Week view** — as event titles inside `WeekDay` cards (loaded via `fetchMonthEvents`)
