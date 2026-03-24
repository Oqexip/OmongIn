<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user.
     */
    public function index()
    {
        $notifications = Notification::forUser(Auth::id())
            ->with(['sender:id,name', 'notifiable'])
            ->orderByDesc('created_at')
            ->paginate(20);

        // Auto-mark as read when user views the index page
        Notification::forUser(Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return back()->with('ok', 'Notifikasi ditandai dibaca.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        Notification::forUser(Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        return back()->with('ok', 'Semua notifikasi ditandai dibaca.');
    }

    /**
     * Return unread notification count as JSON (for badge).
     */
    public function unreadCount()
    {
        $count = Notification::forUser(Auth::id())->unread()->count();

        return response()->json(['count' => $count]);
    }
}
