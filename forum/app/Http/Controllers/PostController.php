<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;


use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        // Eager load thông tin người dùng cùng với bài viết
        $posts = Post::with('user')->get();
        // dd($posts); // Kiểm tra lại dữ liệu
        return view('posts.index', ['posts' => $posts]); // Trả về view posts.index cùng với biến $posts
    }


    public function show(Post $post)
    {
        $post->load('user', 'comments.user', 'categories');
        return view('posts.show', compact('post'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
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
        ]);

        // Xử lý file upload nếu có
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/images/', $filename); // Lưu file vào thư mục storage/app/public/

            // Lưu thông tin file vào bảng bài viết
            $post->update(['image_url' => 'images/' . $filename]);
        }

        return redirect()->route('posts.index')->with('success', 'bài viết đã lưu.');
    }


    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'categories' => 'array'
        ]);

        $post->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        $post->categories()->sync($validated['categories']);

        return redirect()->route('users.index');
    }
}
