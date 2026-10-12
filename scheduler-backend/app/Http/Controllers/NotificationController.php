<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get the authenticated user's notifications.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', $request->user()->user_id)
            ->orderBy('created_at', 'desc');

        if ($request->query('unread')) {
            $query->unread();
        }

        $notifications = $query->get();

        return response()->json($notifications);
    }

    /**
     * Mark a single notification as read.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Notification $notification)
    {
        $this->authorizeNotification($notification);

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read.']);
    }

    /**
     * Mark all notifications for the user as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    /**
     * Delete a notification (optional, for cleanup).
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Notification $notification)
    {
        $this->authorizeNotification($notification);

        $notification->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }

    /**
     * Ensure the user owns the notification.
     */
    private function authorizeNotification(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
    }
}
