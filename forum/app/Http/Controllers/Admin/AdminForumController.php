<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;

class AdminForumController extends Controller
{
    public function index()
    {
        $posts = ForumPost::with('user', 'category')->paginate(10);
        return view('admin.forum.index', compact('posts'));
    }

    public function show($id)
    {
        $post = ForumPost::with('user', 'category')->findOrFail($id);
        return view('admin.forum.show', compact('post'));
    }

    public function destroy($id)
    {
        $post = ForumPost::findOrFail($id);
        $post->delete();
        return redirect()->route('admin.forum.index')->with('success', 'Bài viết đã được xóa.');
    }
}