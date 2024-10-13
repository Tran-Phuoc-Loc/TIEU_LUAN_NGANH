<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        // Tìm bài viết
        $post = Post::findOrFail($postId);

        // Xác thực dữ liệu
        $validated = $request->validate([
            'content' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ]);

        // Tạo bình luận mới
        $comment = new Comment();
        $comment->content = $validated['content'];
        $comment->post_id = $post->id;
        $comment->user_id = Auth::id(); // Lấy ID của người dùng đã đăng nhập
        $comment->parent_id = $validated['parent_id'] ?? null; // Null nếu không phải trả lời bình luận nào

        // Kiểm tra nếu có hình ảnh được tải lên
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comments', 'public');
            $comment->image_url = $imagePath;
        }

        // Lưu bình luận và kiểm tra kết quả
        try {
            $comment->save();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
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
        // Lấy tất cả bình luận cho bài viết cùng với người dùng và các reply lồng nhau
        $comments = Comment::with('user')
            ->where('post_id', $postId)
            ->whereNull('parent_id') // Chỉ lấy bình luận gốc (không có parent_id)
            ->get()
            ->map(function ($comment) {
                // Hàm đệ quy để lấy các replies nhiều cấp
                $fetchReplies = function ($comment) use (&$fetchReplies) {
                    return $comment->replies()->with('user')->get()->map(function ($reply) use ($fetchReplies) {
                        return [
                            'id' => $reply->id,
                            'content' => $reply->content,
                            'created_at' => $reply->created_at,
                            'likes_count' => $reply->likes_count ?? 0,
                            'user' => [
                                'id' => $reply->user->id,
                                'username' => $reply->user->username,
                                'profile_picture' => $reply->user->profile_picture,
                            ],
                            'image_url' => $reply->image_url,
                            // Gọi lại hàm để lấy các replies của reply hiện tại (nếu có)
                            'replies' => $fetchReplies($reply),
                        ];
                    });
                };

                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                    'likes_count' => $comment->likes_count ?? 0,
                    'user' => [
                        'id' => $comment->user->id,
                        'username' => $comment->user->username,
                        'profile_picture' => $comment->user->profile_picture,
                    ],
                    'image_url' => $comment->image_url,
                    'replies' => $fetchReplies($comment), // Gọi hàm để lấy tất cả replies
                ];
            });

        // Trả về JSON chứa danh sách bình luận và replies
        return response()->json([
            'comments' => $comments,
        ]);
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

    // public function reply(Request $request, $commentId)
    // {
    //     $request->validate([
    //         'content' => 'required|string|max:255',
    //     ]);

    //     $comment = Comment::findOrFail($commentId);

    //     $reply = new Comment();
    //     $reply->content = $request->content;
    //     $reply->user_id = Auth::id();
    //     $reply->parent_id = $commentId; // Gán ID bình luận cha
    //     $reply->save();

    //     return response()->json([
    //         'success' => true,
    //         'reply' => [
    //             'user' => [
    //                 'username' => auth()->user()->username,
    //             ],
    //             'content' => $reply->content,
    //             'created_at_diff' => $reply->created_at->diffForHumans(),
    //         ],
    //     ]);
    // }
}
