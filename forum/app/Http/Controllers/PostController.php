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

        return view('users.posts.create'); // Hiển thị trang tạo bài viết
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
            return redirect()->route('users.posts.create')->with('error', 'Có lỗi xảy ra khi lưu bài viết.');
        }
    }

    public function edit(Post $post)
    {
        // Kiểm tra quyền truy cập
        if (Auth::id() !== $post->user->id) {
            // Chuyển hướng lại với thông báo lỗi nếu họ không sở hữu bài đăng
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        // Nếu người dùng có quyền, hiển thị trang chỉnh sửa bài viết
        return view('users.posts.edit', compact('post'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        // Kiểm tra quyền truy cập
        if (Auth::id() !== $post->user->id) {
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
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập để tạo bài viết.');
        }

        // Lấy ID của người dùng đã đăng nhập bằng Auth facade
        $userId = Auth::id();

        // Fetch drafts for the current user
        $drafts = Post::with('user')
            ->where('status', 'draft')
            ->where('user_id', $userId)
            ->paginate(10);

        // Return view with drafts
        return view('users.posts.drafts', compact('drafts'));
    }

    public function published()
    {
    // Kiểm tra người dùng đã đăng nhập
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Bạn phải đăng nhập để xem bài viết.');
    }
    
        // Lấy tất cả bài viết đã xuất bản thuộc về người dùng hiện tại
        $posts = Post::where('status', 'published')
                     ->where('user_id', Auth::id()) // Chỉ lấy bài viết của người dùng hiện tại
                     ->orderBy('created_at', 'desc')
                     ->paginate(10); // Sử dụng paginate nếu cần phân trang
    
        return view('users.posts.published', compact('posts'));
    }
    

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        // Kiểm tra quyền truy cập
        if (Auth::id() !== $post->user->id) {
            abort(403, 'Unauthorized action.');
        }

        $post->delete();

        return redirect()->route('users.posts.drafts')->with('success', 'Bài viết đã được xóa.');
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
