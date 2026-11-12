<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display list of user notifications
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'all');

        $query = Notification::where('user_id', $user->id)->with(['sop', 'bpm']);

        if ($tab === 'unread') {
            $query->unread();
        }

        $notifications = $query->latest('created_at')->paginate(20)->withQueryString();
        $unreadCount = Notification::where('user_id', $user->id)->unread()->count();

        return view('notifications.index', compact('notifications', 'tab', 'unreadCount'));
    }

    /**
     * Mark single notification as read and redirect to its SOP or BPM
     */
    public function read(Notification $notification)
    {
        $user = auth()->user();
        abort_unless($notification->user_id === $user->id, 403);

        $notification->markAsRead();

        if ($notification->bpm_id) {
            return redirect()->route('bpms.show', $notification->bpm_id);
        }

        if ($notification->sop_id) {
            return redirect()->route('sops.show', $notification->sop_id);
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Mark all unread notifications as read
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        Notification::where('user_id', $user->id)->unread()->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai telah dibaca.');
    }
}

