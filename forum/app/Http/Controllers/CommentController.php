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
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $postId)
    {

        // Kiểm tra người dùng đã đăng nhập hay chưa
        if (!Auth::check()) {
            Log::info('User not authenticated');
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập.'], 401); // 401 Unauthorized
        }
        Log::info('User authenticated');

        // Tìm bài viết
        $post = Post::findOrFail($postId);

        // Xác thực dữ liệu
        $validated = $request->validate([
            'content' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ]);
        Log::info('Received postId: ' . $postId);

        // Kiểm tra parent_id (nếu có)
        $parentId = $validated['parent_id'] ?? null;
        Log::info('parentId: ' . $parentId . ', postId: ' . $postId);

        if ($parentId) {
            $parentComment = Comment::where('id', $parentId)->where('post_id', $postId)->first();

            // Nếu không tìm thấy bình luận cha hoặc không thuộc bài viết hiện tại
            if (!$parentComment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bình luận cha không hợp lệ hoặc không thuộc bài viết này.',
                ], 400);
            }

            // Nếu bình luận cha đã là cấp 2 (đã có parent_id)
            if ($parentComment->parent_id) {
                $parentId = $parentComment->parent_id; // Gán lại parent_id để tạo bình luận cùng cấp với cha
            }
        }

        // Tạo bình luận mới
        $comment = new Comment();
        $comment->content = $validated['content'];
        $comment->post_id = $post->id;
        $comment->user_id = Auth::id(); // Lấy ID của người dùng đã đăng nhập
        $comment->parent_id = $parentId; // Dùng parent_id đã xử lý

        // Kiểm tra nếu có hình ảnh được tải lên
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('comments', 'public');

            if (!$imagePath) {
                return response()->json(['success' => false, 'message' => 'Không thể lưu hình ảnh.'], 500);
            }

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
        Log::info('Received comment data', $validated);
        Log::info('Received request', $request->all());
        Log::info('Saving comment for postId: ' . $postId);

        // Luôn trả về JSON
        return response()->json([
            'success' => true,
            'message' => 'Bình luận đã được gửi!',
            'comment' => $comment,
        ], 201);
    }

    public function index($postId)
    {
        $currentUserId = Auth::id();

        // Lấy tất cả bình luận của bài viết
        $allComments = Comment::with('user')
            ->where('post_id', $postId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Hàm đệ quy để sắp xếp bình luận và các trả lời
        $buildCommentsTree = function ($comments, $parentId = null, $depth = 0, $maxDepth = 2) use (&$buildCommentsTree, $currentUserId) {
            if ($depth >= $maxDepth) {
                // Dừng đệ quy nếu đạt đến độ sâu tối đa
                return collect();
            }

            return $comments
                ->filter(function ($comment) use ($parentId) {
                    return $comment->parent_id === $parentId;
                })
                ->map(function ($comment) use ($comments, $buildCommentsTree, $currentUserId, $depth, $maxDepth) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'created_at' => $comment->created_at,
                        'likes_count' => $comment->likes_count ?? 0,
                        'is_owner' => $currentUserId === $comment->user_id,
                        'user' => [
                            'id' => $comment->user->id,
                            'username' => $comment->user->username,
                            'profile_picture' => $comment->user->profile_picture,
                        ],
                        'image_url' => $comment->image_url,
                        // Đệ quy với độ sâu tăng thêm 1
                        'replies' => $buildCommentsTree($comments, $comment->id, $depth + 1, $maxDepth)->values(),
                    ];
                });
        };

        // Gọi hàm đệ quy để xây dựng cây bình luận với độ sâu tối đa là 2
        $commentsTree = $buildCommentsTree($allComments);

        // Trả về kết quả dưới dạng JSON
        return response()->json([
            'comments' => $commentsTree->values(),
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
