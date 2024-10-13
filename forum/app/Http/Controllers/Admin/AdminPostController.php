<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\PostUpdated;
use Illuminate\Support\Facades\Storage;

class AdminPostController extends Controller
{
    // Hiển thị danh sách bài viết với tìm kiếm và lọc
    public function index(Request $request)
    {
        // Truy vấn danh sách bài viết và load thông tin tác giả
        $query = Post::with('author');

        // Tìm kiếm theo tiêu đề
        if ($request->filled('search')) {
            $searchTerm = trim($request->input('search'));
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        // Lọc theo tác giả
        if ($request->filled('author')) {
            $query->where('user_id', $request->input('author'));
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Phân trang 10 bài viết một trang
        $posts = $query->paginate(10);

        // Lấy danh sách tác giả, loại bỏ những user có vai trò là 'admin'
        $authors = User::where('role', '!=', 'admin')->get();

        // Kiểm tra nếu không có bài viết nào được tìm thấy
        if ($posts->isEmpty() && $request->filled('search')) {
            $message = 'Không tìm thấy bài viết nào với từ khóa: ' . $searchTerm;
        } else {
            $message = null;  // Không có thông báo khi có bài viết
        }

        return view('admin.posts.index', compact('posts', 'authors', 'message'));
    }

    // Hiển thị form chỉnh sửa bài viết
    public function edit($id)
    {
        $post = Post::findOrFail($id);  // Tìm bài viết theo ID
        return view('admin.posts.edit', compact('post'));
    }

    // Cập nhật bài viết
    public function update(Request $request, $id)
    {
        // Lấy bài viết theo ID
        $post = Post::findOrFail($id);

        // Validate dữ liệu
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'edit_reason' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Cập nhật bài viết với dữ liệu từ form
        $post->title = $validatedData['title'];
        $post->content = $validatedData['content'];
        $post->edit_reason = $validatedData['edit_reason'];
        $post->status = $validatedData['status'];
        $post->category_id = $validatedData['category_id'];

        // Nếu có file ảnh được tải lên
        if ($request->hasFile('image')) {
            if ($post->image_url) {
                Storage::disk('public')->delete($post->image_url);
            }
            $path = $request->file('image')->store('images', 'public');
            $post->image_url = $path;
        }

        // Gửi thông báo đến người dùng
        $user = User::find($post->user_id); // Lấy user liên quan đến bài viết
        if ($user) {
            $user->notify(new PostUpdated($post)); // Gửi thông báo đến người dùng
        }

        // Lưu các thay đổi
        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã được cập nhật.');
    }

    // Xóa bài viết
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã được xóa');
    }

    // Hành động hàng loạt (bulk action)
    public function bulkAction(Request $request)
    {
        $action = $request->input('bulk_action');
        $postIds = $request->input('post_ids', []);

        if ($action == 'publish') {
            Post::whereIn('id', $postIds)->update(['status' => 'published']);
        } elseif ($action == 'delete') {
            Post::whereIn('id', $postIds)->delete();
        }

        return redirect()->back()->with('success', 'Hành động được thực hiện thành công.');
    }
}
