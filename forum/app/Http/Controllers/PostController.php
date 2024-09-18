<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập để tạo bài viết.');
        }

        return view('posts.create'); // Hiển thị trang tạo bài viết
    }

    public function store(StorePostRequest $request)
    {
        try {
            $userId = Auth::id();

            $post = Post::create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'user_id' => $userId,
                'status' => 'draft',
            ]);

            // Xử lý file upload nếu có
            if ($request->hasFile('image')) {
                $filename = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('public/images/', $filename);
                $post->update(['image_url' => 'images/' . $filename]);
            }

            return redirect()->route('posts.create')->with('success', 'Bài viết đã lưu.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo bài viết: ' . $e->getMessage());
            return redirect()->route('posts.create')->with('error', 'Có lỗi xảy ra khi lưu bài viết.');
        }
    }

    public function edit(Post $post)
    {
        // Check if the authenticated user owns the post
        if (auth()->user()->id !== $post->user->id) {
            // Redirect back with an error message if they don't own the post
            return redirect()->route('posts.index')->with('error', 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        // If the user owns the post, show the edit page
        return view('posts.edit', compact('post'));
    }


    public function update(UpdatePostRequest $request, Post $post)
    {
        // Kiểm tra quyền truy cập
        if (auth()->user()->id !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Cập nhật bài viết với dữ liệu đã xác thực
        $post->update($request->validated());

        // Cập nhật các danh mục nếu có
        if ($request->has('categories')) {
            $post->categories()->sync($request->input('categories'));
        }

        return redirect()->route('users.index')->with('success', 'Bài viết đã được cập nhật.');
    }

    public function drafts()
    {
        $drafts = Post::with('user')->where('status', 'draft')->paginate(10); // Phân trang bài viết draft
        return view('posts.drafts', compact('drafts'));
    }

    public function published()
    {
        // Lấy tất cả bài viết đã xuất bản
        $posts = Post::where('status', 'published')->orderBy('created_at', 'desc')->paginate(10); // Sử dụng paginate nếu cần phân trang

        return view('posts.published', compact('posts'));
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        // Kiểm tra quyền truy cập
        if (auth()->user()->id !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $post->delete();

        return redirect()->route('posts.drafts')->with('success', 'Bài viết đã được xóa.');
    }

    // Thu hồi bài viết
    public function recall($id)
    {
        $post = Post::findOrFail($id);
        $this->authorize('update', $post); // Kiểm tra quyền người dùng

        // Chuyển bài viết về trạng thái draft
        $post->status = 'draft';
        $post->save();

        return redirect()->route('posts.user.published')->with('success', 'Bài viết đã được thu hồi về nháp.');
    }

    public function like($id)
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập.'], 401);
        }

        $post = Post::findOrFail($id); // Tìm bài viết theo ID

        // Kiểm tra xem người dùng đã thích bài viết chưa
        $like = $post->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            // Nếu đã thích, xóa lượt thích
            $like->delete();
            $post->decrement('likes_count'); // Giảm số lượng lượt thích
            $isLiked = false;
        } else {
            // Nếu chưa thích, thêm lượt thích mới
            $post->likes()->create(['user_id' => Auth::id(), 'post_id' => $post->id]); // Cung cấp post_id
            $post->increment('likes_count'); // Tăng số lượng lượt thích
            $isLiked = true;
        }

        return response()->json([
            'success' => true,
            'isLiked' => $isLiked,
            'new_like_count' => $post->likes_count,
            'post' => $post // Có thể gửi lại thông tin bài viết nếu cần
        ]);
    }
}
