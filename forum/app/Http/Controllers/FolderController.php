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

        // Lấy danh sách bài viết đã lưu của người dùng hiện tại
        $savedPosts = Auth::user()->savedPosts()->whereHas('post', function ($query) {
            $query->where('status', 'published'); // Chỉ lấy bài viết còn công khai
        })->get();

        return view('users.folders.index', compact('folders', 'savedPosts'));
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
            ->with(['post.user', 'post.category', 'post.comments.user', 'folder']) // Lấy cả thông tin người viết, danh mục, comment, thư mục
            ->whereHas('post', function ($query) {
                $query->where('status', 'published');
            })
            ->get()
            ->groupBy('folder.name') // Theo nhóm tên thư mục
            ->map(function ($group, $folderName) {
                return [
                    'folder' => $folderName,
                    'posts' => $group->map(function ($savedPost) {
                        return [
                            'id' => $savedPost->post->id,
                            'title' => $savedPost->post->title,
                            'content' => $savedPost->post->content,
                            'image_url' => $savedPost->post->image_url,
                            'like_count' => $savedPost->post->like_count,
                            'comments_count' => $savedPost->post->comments_count,
                            'published_at' => $savedPost->post->published_at ? $savedPost->post->published_at->isoFormat('MMM Do YYYY, h:mm a') : $savedPost->post->created_at->isoFormat('MMM Do YYYY, h:mm a'),
                            'category' => $savedPost->post->category ? $savedPost->post->category->name : 'Không có danh mục',
                            'author' => $savedPost->post->user->username,
                            'author_avatar' => $savedPost->post->user->profile_picture ? asset('storage/' . $savedPost->post->user->profile_picture) : asset('storage/images/avataricon.png'),
                            'comments' => $savedPost->post->comments->map(function ($comment) {
                                return [
                                    'id' => $comment->id,
                                    'user' => $comment->user->username,
                                    'content' => $comment->content,
                                    'created_at' => $comment->created_at->diffForHumans(),
                                    'profile_picture' => $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : asset('storage/images/avataricon.png'),
                                ];
                            }),
                        ];
                    })
                ];
            });

        return response()->json($savedPosts);
    }

    public function unsavePost($postId)
    {
        // Lấy bài viết đã lưu của người dùng hiện tại
        $savedPost = SavedPost::where('user_id', Auth::id())
            ->where('post_id', $postId)
            ->first();

        // Nếu tồn tại thì xóa
        if ($savedPost) {
            $savedPost->delete();

            return response()->json(['success' => true, 'message' => 'Bài viết đã được xóa khỏi danh sách lưu.']);
        }

        // Nếu không tìm thấy bài viết đã lưu
        return response()->json(['success' => false, 'message' => 'Không tìm thấy bài viết đã lưu.']);
    }

    public function getComments($postId)
    {
        $comments = Comment::where('post_id', $postId)
            ->with('user')
            ->with('replies.user') // Lấy thêm replies nếu có
            ->get();

        return response()->json([
            'comments' => $comments
        ]);
    }

    public function deleteFolder($folderId)
    {
        $folder = Folder::where('user_id', Auth::id())->findOrFail($folderId);

        // Xóa toàn bộ bài viết đã lưu trong thư mục
        $folder->savedPosts()->delete();

        // Xóa thư mục
        $folder->delete();

        return redirect()->back()->with('success', 'Thư mục và các bài viết đã lưu đã được xóa.');
    }

    public function renameFolder(Request $request, $folderId)
    {
        $folder = Folder::where('user_id', Auth::id())->findOrFail($folderId);

        // Đổi tên thư mục
        $folder->name = $request->input('new_name');
        $folder->save();

        return redirect()->back()->with('success', 'Thư mục đã được đổi tên thành công.');
    }
}
