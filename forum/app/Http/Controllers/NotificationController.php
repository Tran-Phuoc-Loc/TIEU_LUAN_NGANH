<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Lấy thông báo chưa đọc
        $unreadNotifications = $user->unreadNotifications;

        // Lấy thông báo đã đọc với phân trang
        $readNotifications = $user->notifications()->whereNotNull('read_at')->paginate(10);

        // Lấy thông tin bài viết từ thông báo đầu tiên chưa đọc (nếu có)
        $post = null;
        if ($unreadNotifications->isNotEmpty()) {
            // Giả sử thông báo chứa post_id
            $postId = $unreadNotifications->first()->data['post_id'];
            $post = Post::find($postId);
        }

        return view('notifications.index', compact('unreadNotifications', 'readNotifications', 'post'));
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->route('notifications.index')->with('success', 'Tất cả thông báo đã được đánh dấu là đã đọc.');
    }
}

