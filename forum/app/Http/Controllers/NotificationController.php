<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Lấy tất cả nhóm 
        $groups = Group::all();

        // Lấy thông báo chưa đọc
        $unreadNotifications = $user->unreadNotifications;

        // Lấy thông báo đã đọc với phân trang
        $readNotifications = $user->notifications()->whereNotNull('read_at')->paginate(10);

        // Lấy thông tin bài viết hoặc nhóm từ thông báo đầu tiên chưa đọc (nếu có)
        $post = null;
        $group = null;

        if ($unreadNotifications->isNotEmpty()) {
            $notificationData = $unreadNotifications->first()->data;

            // Kiểm tra nếu thông báo có post_id
            if (isset($notificationData['post_id'])) {
                $post = Post::find($notificationData['post_id']);
            }

            // Kiểm tra nếu thông báo có group_id
            if (isset($notificationData['group_id'])) {
                $group = Group::find($notificationData['group_id']);
            }
        }

        return view('notifications.index', compact('unreadNotifications', 'readNotifications', 'post', 'group', 'groups'));
    }

    // Đánh dấu tất cả thông báo là đã đọc
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return redirect()->route('notifications.index')->with('success', 'Tất cả thông báo đã được đánh dấu là đã đọc.');
    }

    // Đánh dấu thông báo đã đọc
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id); // Tìm thông báo theo ID
        $notification->markAsRead(); // Đánh dấu thông báo là đã đọc

        return redirect()->route('notifications.index')->with('success', 'Thông báo đã được đánh dấu là đã đọc.');
    }
}
