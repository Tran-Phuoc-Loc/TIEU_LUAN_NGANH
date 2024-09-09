<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            // Nếu người dùng đã đăng nhập, lấy tất cả bài viết đã xuất bản
            $posts = Post::with('user')->where('status', 'published')->get();
        } else {
            // Nếu người dùng chưa đăng nhập, chỉ lấy 4 bài viết đã xuất bản
            $posts = Post::with('user')->where('status', 'published')->limit(4)->get();
        }
        dd($posts); // Dừng lại và hiển thị bài viết
        return view('posts.index', ['posts' => $posts]); // Trả về view posts.index cùng với biến $posts
    }

    public function show(Post $post)
    {
        $post->load('user', 'comments.user', 'categories');
        return view('posts.show', compact('post'));
    }

    public function create()
    {
        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (!Auth::check()) {
            // Chuyển hướng về trang danh sách bài viết với thông báo lỗi
            return redirect()->route('posts.index')->with('error', 'Bạn cần đăng nhập để tạo bài viết.');
        }

        // Nếu đã đăng nhập, hiển thị trang tạo bài viết
        return view('posts.create');
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('posts.index')->with('error', 'Bạn cần đăng nhập để tạo bài viết.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $userId = Auth::id(); // Lấy ID của người dùng hiện tại

        $post = Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => $userId,
            'status' => 'draft',
        ]);

        // Xử lý file upload nếu có
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/images/', $filename); // Lưu file vào thư mục storage/app/public/

            // Lưu thông tin file vào bảng bài viết
            $post->update(['image_url' => 'images/' . $filename]);
        }

        return redirect()->route('posts.create')->with('success', 'Bài viết đã lưu.');
    }


    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        if (!Auth::check()) {
            return redirect()->route('posts.index')->with('error', 'Bạn cần đăng nhập để sửa bài viết.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories' => 'array'
        ]);

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        $post->categories()->sync($validated['categories']);

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được cập nhật.');
    }

    public function drafts()
    {
        $drafts = Post::with('user')->where('status', 'draft')->get();
        return view('posts.drafts', ['drafts' => $drafts]);
    }

    public function publish($id)
    {
        // Tìm bài viết theo ID
        $post = Post::findOrFail($id);

        // Cập nhật trạng thái bài viết
        $post->status = 'published';
        $post->save();

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được xuất bản.');
    }
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.drafts')->with('success', 'Bài viết đã được xóa.');
    }
}
