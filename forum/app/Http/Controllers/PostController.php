<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return view('posts.index', compact('posts'));
    }

    // Hiển thị danh sách bài viết với tìm kiếm và lọc
    // public function index(Request $request)
    // {
    //     $query = Post::query();

    //     // Tìm kiếm theo tiêu đề bài viết
    //     if ($request->has('search')) {
    //         $query->where('title', 'like', '%' . $request->search . '%');
    //     }

    //     // Lọc theo danh mục
    //     if ($request->has('category')) {
    //         $query->whereHas('categories', function ($q) use ($request) {
    //             $q->where('name', $request->category);
    //         });
    //     }

    //     $posts = $query->get();

    //     // Lấy danh mục để hiển thị trong form lọc
    //     // $categories = Category::all();

    //     // return view('posts.index', compact('posts', 'categories'));
    //     return view('layouts.app');
    // }

    // // Hiển thị form tạo bài viết
    // public function create()
    // {
    //     return view('posts.create');
    // }

    // // Lưu bài viết mới
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'required',
    //         'content' => 'required',
    //     ]);

    //     $post = new Post();
    //     $post->title = $request->title;
    //     $post->content = $request->content;
    //     $post->user_id = auth()->id();
    //     $post->save();

    //     return redirect()->route('layouts.app');
    // }

    // // Hiển thị chi tiết bài viết
    // public function show(Post $post)
    // {
    //     return view('posts.show', compact('post'));
    // }
}
