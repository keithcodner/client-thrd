<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AdminRoleMiddleware;
use App\Models\Event\Event;
use App\Models\Event\EventGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Carbon\Carbon;

class ManageAdminEventController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', AdminRoleMiddleware::class]);
    }

    public function index(Request $request)
    {
        // Get filter parameters
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $eventType = $request->get('event_type', 'all');
        $userId = $request->get('user_id');
        $status = $request->get('status', 'active');

        // Base query for events
        $query = Event::with(['userFrom:id,firstname,lastname,username,email', 'userTo:id,firstname,lastname,username,email', 'eventGroup'])
            ->whereBetween('event_date_time', [$startDate, $endDate]);

        // Filter by event type
        if ($eventType !== 'all') {
            if ($eventType === 'admin_event') {
                $query->where('type', 'admin_event');
            } elseif ($eventType === 'user_event') {
                $query->where('type', '!=', 'admin_event');
            } else {
                $query->where('type', $eventType);
            }
        }

        // Filter by specific user
        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('user_from_id', $userId)
                  ->orWhere('user_to_id', $userId);
            });
        }

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Get events
        $events = $query->orderBy('event_date_time', 'asc')->get();

        // Get users for filtering dropdown
        $users = User::select('id', 'firstname', 'lastname', 'username', 'email')
            ->orderBy('firstname')
            ->get();

        // Get event statistics
        $statistics = [
            'total_events' => Event::whereBetween('event_date_time', [$startDate, $endDate])->count(),
            'admin_events' => Event::where('type', 'admin_event')->whereBetween('event_date_time', [$startDate, $endDate])->count(),
            'user_events' => Event::where('type', '!=', 'admin_event')->whereBetween('event_date_time', [$startDate, $endDate])->count(),
            'active_events' => Event::where('status', 'active')->whereBetween('event_date_time', [$startDate, $endDate])->count(),
        ];

        return Inertia::render('Admin/EventManagement/EventManagement', [
            'events' => $events,
            'users' => $users,
            'statistics' => $statistics,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'event_type' => $eventType,
                'user_id' => $userId,
                'status' => $status,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'event_date_time' => 'required|date',
            'event_date_time_start_range' => 'nullable|date',
            'event_date_time_end_range' => 'nullable|date|after_or_equal:event_date_time_start_range',
            'type' => 'required|in:admin_event,self_event,circle,meeting,appointment',
            'status' => 'required|in:active,inactive,cancelled,completed',
            'category' => 'nullable|string|max:100',
            'link' => 'nullable|url|max:500',
            'isVisibleToOthers' => 'boolean',
            'user_to_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $event = Event::create([
            'user_from_id' => Auth::id(),
            'user_to_id' => $request->user_to_id,
            'event_an_id' => 'ADMIN_' . now()->format('YmdHis') . '_' . Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'event_date_time' => $request->event_date_time,
            'event_date_time_start_range' => $request->event_date_time_start_range,
            'event_date_time_end_range' => $request->event_date_time_end_range,
            'type' => $request->type,
            'type_second' => $request->type === 'admin_event' ? 'site_wide' : null,
            'status' => $request->status,
            'category' => $request->category,
            'link' => $request->link,
            'isVisibleToOthers' => $request->type === 'admin_event' ? true : ($request->isVisibleToOthers ?? false),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Event created successfully.');
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'event_date_time' => 'required|date',
            'event_date_time_start_range' => 'nullable|date',
            'event_date_time_end_range' => 'nullable|date|after_or_equal:event_date_time_start_range',
            'type' => 'required|in:admin_event,self_event,circle,meeting,appointment',
            'status' => 'required|in:active,inactive,cancelled,completed',
            'category' => 'nullable|string|max:100',
            'link' => 'nullable|url|max:500',
            'isVisibleToOthers' => 'boolean',
            'user_to_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $event->update([
            'user_to_id' => $request->user_to_id,
            'name' => $request->name,
            'description' => $request->description,
            'event_date_time' => $request->event_date_time,
            'event_date_time_start_range' => $request->event_date_time_start_range,
            'event_date_time_end_range' => $request->event_date_time_end_range,
            'type' => $request->type,
            'type_second' => $request->type === 'admin_event' ? 'site_wide' : $event->type_second,
            'status' => $request->status,
            'category' => $request->category,
            'link' => $request->link,
            'isVisibleToOthers' => $request->type === 'admin_event' ? true : ($request->isVisibleToOthers ?? $event->isVisibleToOthers),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Event updated successfully.');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->back()->with('success', 'Event deleted successfully.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_ids' => 'required|array',
            'event_ids.*' => 'exists:event,id',
            'status' => 'required|in:active,inactive,cancelled,completed'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        Event::whereIn('id', $request->event_ids)
            ->update(['status' => $request->status, 'updated_at' => now()]);

        return redirect()->back()->with('success', 'Events status updated successfully.');
    }

    public function getCalendarEvents(Request $request)
    {
        $startDate = $request->get('start');
        $endDate = $request->get('end');
        $eventType = $request->get('event_type', 'all');
        $userId = $request->get('user_id');

        $query = Event::with(['userFrom:id,firstname,lastname,username', 'userTo:id,firstname,lastname,username'])
            ->whereBetween('event_date_time', [$startDate, $endDate]);

        // Apply filters
        if ($eventType !== 'all') {
            if ($eventType === 'admin_event') {
                $query->where('type', 'admin_event');
            } elseif ($eventType === 'user_event') {
                $query->where('type', '!=', 'admin_event');
            } else {
                $query->where('type', $eventType);
            }
        }

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('user_from_id', $userId)
                  ->orWhere('user_to_id', $userId);
            });
        }

        $events = $query->get()->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->name,
                'start' => $event->event_date_time,
                'end' => $event->event_date_time_end_range ?: $event->event_date_time,
                'description' => $event->description,
                'type' => $event->type,
                'status' => $event->status,
                'category' => $event->category,
                'user_from' => $event->userFrom,
                'user_to' => $event->userTo,
                'link' => $event->link,
                'isVisibleToOthers' => $event->isVisibleToOthers,
                'color' => $this->getEventColor($event->type, $event->status),
            ];
        });

        return response()->json($events);
    }

    private function getEventColor($type, $status)
    {
        if ($status === 'cancelled') return '#ef4444'; // red
        if ($status === 'completed') return '#10b981'; // green
        if ($status === 'inactive') return '#6b7280'; // gray

        switch ($type) {
            case 'admin_event':
                return '#3b82f6'; // blue
            case 'circle':
                return '#f59e0b'; // amber
            case 'meeting':
                return '#8b5cf6'; // violet
            case 'appointment':
                return '#06b6d4'; // cyan
            default:
                return '#64748b'; // slate
        }
    }

    public function getUserEvents($userId, Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $events = Event::with(['userFrom:id,firstname,lastname,username', 'userTo:id,firstname,lastname,username'])
            ->where(function ($q) use ($userId) {
                $q->where('user_from_id', $userId)
                  ->orWhere('user_to_id', $userId);
            })
            ->whereBetween('event_date_time', [$startDate, $endDate])
            ->orderBy('event_date_time', 'asc')
            ->get();

        // Check if it's an AJAX request and return JSON, otherwise return Inertia response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($events);
        }

        return inertia('Admin/EventManagement/EventManagement', [
            'events' => $events,
        ]);
    }
}
