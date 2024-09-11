<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function create()
    {
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
        $this->authorize('update', $post);
        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validated()); // Cập nhật bài viết với dữ liệu đã xác thực

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

    public function publish($id)
    {
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);

        $post->status = 'published';
        $post->save();

        return redirect()->route('posts.published')->with('success', 'Bài viết đã được xuất bản.'); // Chuyển hướng về danh sách bài viết đã xuất bản
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.drafts')->with('success', 'Bài viết đã được xóa.');
    }
}
