# Calendar System Documentation

## Overview

The THRD calendar system allows users to create, view, edit, and delete personal time blocks (events). Each event is owned by the creating user, backed by an `event_groups` record for future group-event extensibility (anyone can invite others to join an event group).

---

## Table of Contents

1. [Architecture](#architecture)
2. [Database Schema](#database-schema)
3. [API Endpoints](#api-endpoints)
4. [Request & Response Shapes](#request--response-shapes)
5. [Event Group Flow](#event-group-flow)
6. [Event Types](#event-types)

---

## Architecture

### Backend
- **Controller**: `api/app/Http/Controllers/Event/EventController.php`
- **Models**:
  - `api/app/Models/Event/Event.php` — `event` table
  - `api/app/Models/Event/EventGroup.php` — `event_groups` table
- **Migrations**:
  - `api/database/migrations/2024_03_01_000000_create_event_table.php`
  - `api/database/migrations/2024_03_01_000000_create_event_groups_table.php`
  - `api/database/migrations/2026_03_26_000001_add_event_group_id_and_color_to_event_table.php`
- **Routes**: `api/routes/api.php` under `CALENDAR / EVENT ROUTES`

### Frontend
- **Service**: `mobile/services/calendarService.ts`
- **Screens**:
  - Month: `mobile/app/(app)/(tabs)/(calendar)/index.tsx`
  - Week:  `mobile/app/(app)/(tabs)/(calendar)/week.tsx`
  - Day:   `mobile/app/(app)/(tabs)/(calendar)/day.tsx`
  - List:  `mobile/app/(app)/(tabs)/(calendar)/list.tsx`
- **Components**:
  - `mobile/components/calendar/month/` — MonthView, CalendarGrid, DayCell, ViewTabs, CalendarHeader
  - `mobile/components/calendar/week/` — WeekDay, SuggestedOverlaps, SuggestedOverlapCard, WeekListItem
  - `mobile/components/calendar/day/` — DayHeader, DayTimeline, DayTimeBlock, TimePickerModal
  - `mobile/components/calendar/CreateTimeBlock.tsx` — "Add Time Block" bottom sheet modal
  - `mobile/components/calendar/EditTimeBlock.tsx` — "Edit Time Block" bottom sheet modal

---

## Database Schema

### Table: `event`

| Column                       | Type         | Notes                                   |
|------------------------------|--------------|-----------------------------------------|
| `id`                         | BIGINT PK    | Auto-increment                          |
| `user_from_id`               | INT          | FK → users.id (event creator)           |
| `event_group_id`             | BIGINT NULL  | FK → event_groups.id                    |
| `event_an_id`                | VARCHAR(255) | UUID assigned at creation               |
| `name`                       | VARCHAR(255) | Event label (e.g. "Focus Time")         |
| `event_date_time`            | DATETIME     | Main date-time (mirrors start_range)    |
| `event_date_time_start_range`| DATETIME NULL| Start time                              |
| `event_date_time_end_range`  | DATETIME NULL| End time                                |
| `description`                | TEXT NULL    | Optional notes                          |
| `type`                       | VARCHAR(100) | `self_event` for personal blocks        |
| `status`                     | VARCHAR(100) | `active` / `cancelled`                  |
| `color`                      | VARCHAR(20)  | Hex color e.g. `#ADC178`               |
| `category`                   | VARCHAR(50)  | Optional category string                |
| `created_at`                 | TIMESTAMP    |                                         |
| `updated_at`                 | TIMESTAMP    |                                         |

### Table: `event_groups`

| Column       | Type         | Notes                                        |
|--------------|--------------|----------------------------------------------|
| `id`         | BIGINT PK    | Auto-increment                               |
| `event_id`   | INT NULL     | FK → event.id (primary event for this group) |
| `user_id`    | INT NULL     | FK → users.id (group owner)                  |
| `group_name` | VARCHAR(255) | Matches the event name/label                 |
| `type`       | VARCHAR(50)  | `self_event`                                 |
| `status`     | VARCHAR(50)  | `active` / `cancelled`                       |
| `created_at` | DATETIME     |                                              |
| `updated_at` | DATETIME NULL|                                              |

---

## API Endpoints

All routes are under `auth:sanctum` middleware.

| Method   | URI                       | Action                          |
|----------|---------------------------|---------------------------------|
| `GET`    | `/calendar/events`        | List events for a given month   |
| `GET`    | `/calendar/events/{id}`   | Fetch a single event            |
| `POST`   | `/calendar/events`        | Create event (+ event group)    |
| `PUT`    | `/calendar/events/{id}`   | Update an event                 |
| `DELETE` | `/calendar/events/{id}`   | Delete an event (+ its group)   |

---

## Request & Response Shapes

### GET `/calendar/events?year=2026&month=3`

Returns events for the authenticated user in the given month.

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "user_from_id": 5,
      "event_group_id": 3,
      "name": "Focus Time",
      "event_date_time": "2026-03-26T09:00:00.000Z",
      "event_date_time_start_range": "2026-03-26T09:00:00.000Z",
      "event_date_time_end_range": "2026-03-26T10:00:00.000Z",
      "color": "#ADC178",
      "type": "self_event",
      "status": "active"
    }
  ]
}
```

### POST `/calendar/events`

**Request body:**
```json
{
  "name": "Focus Time",
  "start_at": "2026-03-26T09:00:00.000Z",
  "end_at": "2026-03-26T10:00:00.000Z",
  "color": "#ADC178"
}
```

**Response:** `201 Created` with the created event object.

### PUT `/calendar/events/{id}`

**Request body** (all fields optional):
```json
{
  "name": "Studio Session",
  "start_at": "2026-03-26T14:00:00.000Z",
  "end_at": "2026-03-26T16:00:00.000Z"
}
```

### DELETE `/calendar/events/{id}`

Deletes the event and its owning event group (if this event is the primary event of the group).

**Response:** `200 OK`
```json
{ "message": "Event deleted." }
```

---

## Event Group Flow

When a user creates an event:

1. An `event_groups` row is created with:
   - `user_id` = authenticated user's ID
   - `group_name` = the event label the user typed
   - `event_id` = `null` (temporarily)

2. An `event` row is created with:
   - `event_group_id` = the new group's ID
   - all other fields populated from the request

3. The `event_groups` row is updated:
   - `event_id` = the new event's ID

This structure means any self-event can later be promoted to a group event (other users invited) without changing the event record itself.

---

## Event Types

| `type` value  | Description                              |
|---------------|------------------------------------------|
| `self_event`  | Personal time block created by the user  |

More types may be added in future (e.g. `circle_event`, `invite_event`).
