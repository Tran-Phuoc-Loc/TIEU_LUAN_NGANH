<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumComment;
use Illuminate\Support\Facades\Auth;

class ForumCommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        // Xác thực dữ liệu
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Tạo bình luận mới
        ForumComment::create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(), // Lấy ID người dùng đang đăng nhập
            'forum_post_id' => $postId, // ID của bài viết diễn đàn
        ]);

        return redirect()->route('forums.show', $postId)->with('success', 'Bình luận đã được thêm thành công.');
    }
}
