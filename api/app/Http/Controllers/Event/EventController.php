<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Event\Event;
use App\Models\Event\EventGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    /**
     * GET /calendar/events?year=YYYY&month=MM
     * Returns all events owned by the authenticated user for the given month.
     */
    public function index(Request $request)
    {
        $user  = Auth::user();
        $year  = (int) $request->query('year',  now()->year);
        $month = (int) $request->query('month', now()->month);

        $events = Event::where('user_from_id', $user->id)
            ->where('type', 'self_event')
            ->whereYear('event_date_time_start_range', $year)
            ->whereMonth('event_date_time_start_range', $month)
            ->orderBy('event_date_time_start_range')
            ->get();

        return response()->json(['data' => $events]);
    }

    /**
     * GET /calendar/events/{id}
     */
    public function show($id)
    {
        $user  = Auth::user();
        $event = Event::where('id', $id)
            ->where('user_from_id', $user->id)
            ->firstOrFail();

        return response()->json(['data' => $event->load('eventGroup')]);
    }

    /**
     * POST /calendar/events
     * Creates an EventGroup then an Event, then links event_id back to the group.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'start_at' => 'required|date',
                'end_at'   => 'required|date|after:start_at',
                'color'    => 'nullable|string|max:255',
            ]);

            // Convert ISO 8601 dates to MySQL DATETIME format using the new function
            $startAt = $this->convertToMySQLDateTime($validated['start_at']);
            $endAt = $this->convertToMySQLDateTime($validated['end_at']);

            $event = DB::transaction(function () use ($user, $validated, $startAt, $endAt) {
                // 1. Create the event group
                $group = EventGroup::create([
                    'user_id'    => $user->id,
                    'group_name' => $validated['name'],
                    'type'       => 'self_event',
                    'status'     => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info('Event group created', ['group_id' => $group->id]);

                // 2. Create the event record
                $event = Event::create([
                    'user_from_id'                => $user->id,
                    'event_group_id'              => $group->id,
                    'event_an_id'                 => (string) Str::uuid(),
                    'name'                        => $validated['name'],
                    'event_date_time'             => $startAt,
                    'event_date_time_start_range' => $startAt,
                    'event_date_time_end_range'   => $endAt,
                    'color'                       => $validated['color'] ?? '#ADC178',
                    'type'                        => 'self_event',
                    'status'                      => 'active',
                    'created_at'                  => now(),
                    'updated_at'                  => now(),
                ]);

                Log::info('Event created', ['event_id' => $event->id]);

                // 3. Link event_id back onto the group
                $group->update(['event_id' => $event->id]);

                Log::info('Event group updated with event_id', ['group_id' => $group->id, 'event_id' => $event->id]);

                return $event->load('eventGroup');
            });

            return response()->json(['data' => $event], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('An unexpected error occurred', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * PUT /calendar/events/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $user  = Auth::user();
            $event = Event::where('id', $id)
                ->where('user_from_id', $user->id)
                ->firstOrFail();

            $validated = $request->validate([
                'name'     => 'sometimes|required|string|max:255',
                'start_at' => 'sometimes|required|date',
                'end_at'   => 'sometimes|required|date|after:start_at',
                'color'    => 'nullable|string|max:255',
            ]);

            // Convert ISO 8601 dates to MySQL DATETIME format
            $startAt = isset($validated['start_at']) ? $this->convertToMySQLDateTime($validated['start_at']) : null;
            $endAt = isset($validated['end_at']) ? $this->convertToMySQLDateTime($validated['end_at']) : null;

            $updateData = array_filter([
                'name'                        => $validated['name']     ?? null,
                'event_date_time'             => $startAt,
                'event_date_time_start_range' => $startAt,
                'event_date_time_end_range'   => $endAt,
                'color'                       => $validated['color']    ?? null,
                'updated_at'                  => now(),
            ], fn($v) => $v !== null);

            $event->update($updateData);

            // Sync group name if name changed
            if (isset($validated['name']) && $event->eventGroup) {
                $event->eventGroup->update([
                    'group_name' => $validated['name'],
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['data' => $event->fresh()->load('eventGroup')]);
        } catch (ValidationException $e) {
            Log::error('Validation failed during update', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('An unexpected error occurred during update', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * DELETE /calendar/events/{id}
     */
    public function destroy($id)
    {
        try {
            $user  = Auth::user();
            $event = Event::where('id', $id)
                ->where('user_from_id', $user->id)
                ->firstOrFail();

            // Delete associated group if this event owns it
            if ($event->eventGroup && $event->eventGroup->event_id === $event->id) {
                $event->eventGroup->delete();
            }

            $event->delete();

            return response()->json(['message' => 'Event deleted.']);
        } catch (ModelNotFoundException $e) {
            Log::error('Event not found during delete', ['id' => $id]);
            return response()->json(['error' => 'Event not found'], 404);
        } catch (Exception $e) {
            Log::error('An unexpected error occurred during delete', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Converts an ISO 8601 date string to MySQL DATETIME format.
     *
     * @param string $isoDate
     * @return string
     */
    private function convertToMySQLDateTime(string $isoDate): string
    {
        return Carbon::parse($isoDate)->format('Y-m-d H:i:s');
    }
}
