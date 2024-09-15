<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        // Xác thực dữ liệu
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        // Tìm bài viết
        $post = Post::findOrFail($postId);

        // Tạo bình luận mới
        $comment = new Comment();
        $comment->content = $request->content;
        $comment->post_id = $post->id;
        $comment->user_id = auth()->id(); // Lấy ID của người dùng đã đăng nhập
        $comment->save();

        // Chuyển hướng lại về trang bài viết
        return redirect()->back()->with('success', 'Bình luận đã được gửi!');
    }
}