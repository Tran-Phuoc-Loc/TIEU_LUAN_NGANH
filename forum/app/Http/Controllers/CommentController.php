<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        // Xác thực dữ liệu
        $request->validate([
            'content' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Tìm bài viết
        $post = Post::findOrFail($postId);

        // Tạo bình luận mới
        $comment = new Comment();
        $comment->content = $request->content;
        $comment->post_id = $post->id;
        $comment->user_id = auth()->id(); // Lấy ID của người dùng đã đăng nhập

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comments', 'public');
            $comment->image_url = $imagePath;
        }

        if (!$comment->save()) {
            return response()->json(['success' => false, 'message' => 'Không thể lưu bình luận.']);
        }

        // Kiểm tra xem yêu cầu có phải AJAX không
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã được gửi!',
                'comment' => $comment->load('user'), // Tải thông tin người dùng để hiển thị tên
            ]);
        }

        // Nếu không phải AJAX, chuyển hướng lại trang trước với thông báo
        return redirect()->back()->with('success', 'Bình luận đã được gửi!');
    }

    public function index($postId)
    {
        $comments = Comment::with('user')->where('post_id', $postId)->get()->map(function ($comment) {
            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at, // Trả về đối tượng Carbon
                'likes_count' => $comment->likes_count ?? 0, // Đặt giá trị mặc định là 0
                'user' => [
                    'id' => $comment->user->id,
                    'username' => $comment->user->username,
                    'avatar_url' => $comment->user->avatar_url,
                ],
                'image_url' => $comment->image_url,
            ];
        });

        // Trả về JSON chứa danh sách bình luận
        return response()->json([
            'comments' => $comments,
        ]);
    }

    public function show($postId)
    {
        $post = Post::with('user')->findOrFail($postId);
        $comments = Comment::where('post_id', $postId)->with('user')->get();

        // Log::info('Post:', ['post' => $post]);
        // Log::info('Comments:', ['comments' => $comments]);
        // dd($post, $comments); // Hiển thị dữ liệu để kiểm tra

        return view('users.index', ['post' => $post, 'comments' => $comments]);
    }

    public function like($id)
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập.'], 401);
        }
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Bình luận không tồn tại.']);
        }

        // Kiểm tra xem người dùng đã thích bình luận này chưa
        $like = $comment->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            // Nếu đã thích, xóa like
            $like->delete();
            $comment->likes_count--;
        } else {
            // Nếu chưa thích, thêm like
            $comment->likes()->create(['user_id' => Auth::id()]);
            $comment->likes_count++;
        }

        $comment->save();

        return response()->json([
            'success' => true,
            'new_like_count' => $comment->likes_count,
        ]);
    }
}
