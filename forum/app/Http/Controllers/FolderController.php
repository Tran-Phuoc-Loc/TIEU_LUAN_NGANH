<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\SavedPost;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class FolderController extends Controller
{
    // Phương thức để hiển thị danh sách thư mục
    public function index()
    {
        // Lấy danh sách thư mục của người dùng
        $folders = Folder::where('user_id', Auth::id())->get();

        return view('users.folders.index', compact('folders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $folder = Folder::create([
            'name' => $validated['name'],
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'folder_id' => $folder->id,
        ]);
    }

    public function getSavedPosts()
    {
        // Lấy danh sách bài viết đã lưu của người dùng hiện tại
        $savedPosts = SavedPost::where('user_id', Auth::id())
            ->with('post.user', 'post.category') // Lấy cả thông tin người viết và danh mục
            ->get()
            ->map(function ($savedPost) {
                return [
                    'id' => $savedPost->post->id,
                    'title' => $savedPost->post->title,
                    'content' => $savedPost->post->content,
                    'image_url' => $savedPost->post->image_url,
                    'like_count' => $savedPost->post->like_count,
                    'comments_count' => $savedPost->post->comments_count,
                    'published_at' => $savedPost->published_at ? $savedPost->published_at->isoFormat('MMM Do YYYY, h:mm a') : $savedPost->created_at->isoFormat('MMM Do YYYY, h:mm a'),
                    'category' => $savedPost->post->category ? $savedPost->post->category->name : 'Không có danh mục',
                    'author' => $savedPost->post->user->username,
                    'author_avatar' => $savedPost->post->user->profile_picture ? asset('storage/' . $savedPost->post->user->profile_picture) : asset('storage/images/avataricon.png'),
                ];
            });

        return response()->json($savedPosts);
    }
    public function getComments($postId) {
        $comments = Comment::where('post_id', $postId)->with('user')->get();
        return response()->json([
            'comments' => $comments
        ]);
    }
    
}
