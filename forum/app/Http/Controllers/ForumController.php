<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumPost;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index()
    {
        // Lấy tất cả danh mục và bài viết
        $categories = ForumCategory::with('posts')->get();
        $posts = ForumPost::with('category', 'user')->get();

        return view('users.forums.index', compact('categories', 'posts'));
    }

    public function show($id)
    {
        $categories = ForumCategory::with('posts')->get();
        $forumPost = ForumPost::with('user', 'comments.user')->findOrFail($id); // Tải bài viết cùng với thông tin người dùng và các bình luận
        return view('users.forums.show', compact('categories', 'forumPost')); // Trả về view bài viết chi tiết
    }    

    public function create()
    {
        // Trả về view để tạo bài viết mới
        return view('users.forums.create'); // Đảm bảo view này tồn tại
    }

    public function showCategory($id)
    {
        // Hiển thị bài viết trong một danh mục
        $category = ForumCategory::with('posts')->findOrFail($id);
        return view('users.forums.category', compact('category'));
    }

}
