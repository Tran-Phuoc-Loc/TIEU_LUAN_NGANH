<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Lấy thông báo chưa đọc
        $unreadNotifications = $user->unreadNotifications;
    
        // Lấy thông báo đã đọc
        $readNotifications = $user->notifications()->whereNotNull('read_at')->paginate(10);
    
        // Đánh dấu tất cả thông báo chưa đọc là đã đọc
        $user->unreadNotifications->markAsRead(); 
        // dd($readNotifications);
        // dd($readNotifications->items());

        return view('notifications.index', compact('unreadNotifications', 'readNotifications'));
    }
    
    // Đánh dấu thông báo
    public function markAllAsRead()
{
    // Đánh dấu tất cả thông báo chưa đọc của người dùng hiện tại là đã đọc
    auth()->user()->unreadNotifications->markAsRead();
    
    return redirect()->route('notifications.index')->with('success', 'Tất cả thông báo đã được đánh dấu là đã đọc.');
}

}
