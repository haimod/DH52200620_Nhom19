<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Đánh dấu 1 thông báo cụ thể là đã đọc
     */
    public function markRead($id)
    {
       // $notification = Auth::user()->notifications()->find($id);
            $notification = Auth::user()
                ->notifications
                ->where('id', $id)
                ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return back();
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Đã đánh dấu tất cả là đã đọc.');
    }
}