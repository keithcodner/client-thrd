<?php

namespace App\Http\Controllers\Notifications;

use App\Models\Notification;
use App\Models\Circles\CircleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

/**
 * NotificationsController
 * 
 * Handles all notification-related operations including:
 * - Fetching notifications with filtering by type
 * - Marking notifications as read
 * - Fetching notification details with related data
 * - Getting unread notification counts
 * 
 * **Notification Types:**
 * - circle_request: Invitation to join a circle
 * - message: New message notification
 * - calendar: Calendar event notification
 * - system: System announcements
 * 
 * **Status Values:**
 * - unread: New notification
 * - read: User has viewed the notification
 */
class NotificationsController extends Controller
{
    /**
     * Get notifications for the authenticated user
     * Supports filtering by type and pagination
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotifications(Request $request)
    {
        try {
            $user = $request->user();
            
            $validated = $request->validate([
                'type' => 'nullable|string|in:circle_request,message,calendar,system',
                'status' => 'nullable|string|in:read,unread',
                'limit' => 'nullable|integer|min:1|max:100',
                'offset' => 'nullable|integer|min:0',
            ]);

            $query = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc');

            // Filter by type if provided
            if (isset($validated['type'])) {
                $query->where('type', $validated['type']);
            }

            // Filter by status if provided
            if (isset($validated['status'])) {
                $query->where('status', $validated['status']);
            }

            $limit = $validated['limit'] ?? 30;
            $offset = $validated['offset'] ?? 0;

            $notifications = $query->skip($offset)
                ->take($limit)
                ->get();

            // Load related user data for notifications
            $notifications = $notifications->map(function ($notification) {
                $data = $notification->toArray();
                
                // Load sender info if from_id exists
                if ($notification->from_id) {
                    $fromUser = User::find($notification->from_id);
                    if ($fromUser) {
                        $data['from_user'] = [
                            'id' => $fromUser->id,
                            'name' => $fromUser->name,
                            'firstname' => $fromUser->firstname,
                            'username' => $fromUser->username,
                        ];
                    }
                }

                return $data;
            });

            // Check if there are more notifications
            $hasMore = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->skip($offset + $limit)
                ->take(1)
                ->exists();

            Log::info('Notifications fetched', [
                'user_id' => $user->id,
                'count' => $notifications->count(),
                'type' => $validated['type'] ?? 'all',
            ]);

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'hasMore' => $hasMore,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $request->user()->id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications.',
            ], 500);
        }
    }

    /**
     * Get a single notification by ID with full details
     * Includes related circle request data if applicable
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotificationById(Request $request)
    {
        try {
            $user = $request->user();
            
            $validated = $request->validate([
                'notification_id' => 'required|integer|exists:notifications,id',
            ]);

            $notification = Notification::find($validated['notification_id']);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found.',
                ], 404);
            }

            // Verify user owns this notification
            if ($notification->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to notification.',
                ], 403);
            }

            $data = $notification->toArray();

            // Load sender info
            if ($notification->from_id) {
                $fromUser = User::find($notification->from_id);
                if ($fromUser) {
                    $data['from_user'] = [
                        'id' => $fromUser->id,
                        'name' => $fromUser->name,
                        'firstname' => $fromUser->firstname,
                        'username' => $fromUser->username,
                    ];
                }
            }

            // Load circle request details if this is a circle_request notification
            if ($notification->type === 'circle_request' && $notification->fk_circle_item_post_id) {
                $circleRequest = CircleRequest::with(['circle', 'requester'])
                    ->where('circle_id', $notification->fk_circle_item_post_id)
                    ->where('requesting_to_join_user_id', $user->id)
                    ->where('status', 'pending')
                    ->first();

                if ($circleRequest) {
                    $data['circle_request'] = [
                        'id' => $circleRequest->id,
                        'circle_id' => $circleRequest->circle_id,
                        'circle_name' => $circleRequest->circle->name ?? 'Unknown Circle',
                        'requester_id' => $circleRequest->requester_user_id,
                        'requester_name' => $circleRequest->requester->name ?? 'Unknown',
                        'status' => $circleRequest->status,
                        'created_at' => $circleRequest->created_at,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'notification' => $data,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching notification: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $request->user()->id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notification.',
            ], 500);
        }
    }

    /**
     * Mark a notification as read
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    {
        try {
            $user = $request->user();
            
            $validated = $request->validate([
                'notification_id' => 'required|integer|exists:notifications,id',
            ]);

            $notification = Notification::find($validated['notification_id']);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found.',
                ], 404);
            }

            // Verify user owns this notification
            if ($notification->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to notification.',
                ], 403);
            }

            // Update status to read
            $notification->update([
                'status' => 'read',
            ]);

            Log::info('Notification marked as read', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $request->user()->id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read.',
            ], 500);
        }
    }

    /**
     * Get unread notification count
     * Optionally filter by type
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $user = $request->user();
            
            $validated = $request->validate([
                'type' => 'nullable|string|in:circle_request,message,calendar,system',
            ]);

            $query = Notification::where('user_id', $user->id)
                ->where('status', 'unread');

            if (isset($validated['type'])) {
                $query->where('type', $validated['type']);
            }

            $count = $query->count();

            return response()->json([
                'success' => true,
                'unread_count' => $count,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $request->user()->id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count.',
            ], 500);
        }
    }
}
