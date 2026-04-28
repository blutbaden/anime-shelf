<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        auth()->user()->unreadNotifications->markAsRead();
        return view('notifications', compact('notifications'));
    }

    public function markRead($id)
    {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->noContent();
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }
}
