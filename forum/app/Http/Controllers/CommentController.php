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

        // Kiểm tra tránh bình luận đệ quy (parent_id không thể là chính nó)
        if (isset($validated['parent_id']) && $validated['parent_id'] == $post->id) {
            return response()->json(['success' => false, 'message' => 'Không thể bình luận vào chính bài viết của mình.'], 400);
        }

        // Tạo bình luận mới
        $comment = new Comment();
        $comment->content = $validated['content'];
        $comment->post_id = $post->id;
        $comment->user_id = Auth::id(); // Lấy ID của người dùng đã đăng nhập
        $comment->parent_id = $validated['parent_id'] ?? null;

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

        // Gắn `is_owner = true` cho bình luận vừa tạo
        $comment->is_owner = true;

        // Tải thông tin người dùng để hiển thị
        $comment->load('user');

        // Kiểm tra xem yêu cầu có phải AJAX không
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã được gửi!',
                'comment' => $comment,
            ]);
        }

        // Nếu không phải AJAX, chuyển hướng lại trang trước với thông báo
        return redirect()->back()->with('success', 'Bình luận đã được gửi!');
    }

    public function index($postId)
{
    $currentUserId = Auth::id();

    // Lấy tất cả các bình luận gốc (cấp 1)
    $comments = Comment::with('user')
        ->where('post_id', $postId)
        ->whereNull('parent_id')
        ->orderBy('created_at', 'asc')
        ->get();

    // Lấy tất cả replies (cấp 2) của bài viết, bất kể chúng là reply của ai
    $replies = Comment::with('user')
        ->where('post_id', $postId)
        ->whereNotNull('parent_id')
        ->orderBy('created_at', 'asc')
        ->get();

    // Kết hợp các replies vào đúng vị trí của bình luận cha
    $formattedComments = $comments->map(function ($comment) use ($replies, $currentUserId) {
        // Lọc ra tất cả các replies của bình luận hiện tại
        $commentReplies = $replies->filter(function ($reply) use ($comment) {
            return $reply->parent_id == $comment->id || $reply->parent_id != null;
        });

        // Đưa các replies vào cùng cấp với bình luận cha
        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'created_at' => $comment->created_at,
            'likes_count' => $comment->likes_count ?? 0,
            'is_owner' => Auth::id() === $comment->user_id,
            'user' => [
                'id' => $comment->user->id,
                'username' => $comment->user->username,
                'profile_picture' => $comment->user->profile_picture,
            ],
            'image_url' => $comment->image_url,
            'replies' => $commentReplies->map(function ($reply) use ($currentUserId) {
                return [
                    'id' => $reply->id,
                    'content' => $reply->content,
                    'created_at' => $reply->created_at,
                    'likes_count' => $reply->likes_count ?? 0,
                    'is_owner' => $currentUserId === $reply->user_id,
                    'user' => [
                        'id' => $reply->user->id,
                        'username' => $reply->user->username,
                        'profile_picture' => $reply->user->profile_picture,
                    ],
                    'image_url' => $reply->image_url,
                ];
            })->values(),
        ];
    });

    // Trả về kết quả dưới dạng JSON
    return response()->json([
        'comments' => $formattedComments,
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

    public function destroy($postId, $commentId)
    {
        Log::info("Attempting to delete comment with ID: {$commentId}");

        // Tìm bình luận theo ID
        $comment = Comment::find($commentId);

        if (!$comment) {
            Log::info("Comment not found for ID: {$commentId}"); // Thêm thông tin log
            return response()->json(['success' => false, 'message' => 'ID bình luận không hợp lệ.'], 404);
        }

        // Kiểm tra quyền sở hữu bình luận
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa bình luận này.'], 403);
        }

        // Ghi log bình luận con trước khi xóa
        $replies = Comment::where('parent_id', $comment->id)->get();
        Log::info("Replies before deletion: ", $replies->toArray());
        foreach ($replies as $reply) {
            $reply->parent_id = null; // Đặt parent_id thành null để chuyển lên làm cha
            $reply->save();
        }
        // Ghi log bình luận con sau khi chuyển đổi
        $updatedReplies = Comment::where('parent_id', null)->get(); // Lấy lại các bình luận đã được chuyển
        Log::info("Replies after deletion: ", $updatedReplies->toArray());
        // Xóa bình luận
        $comment->delete();

        return response()->json(['success' => true, 'message' => 'Bình luận đã được xóa.']);
    }
}
