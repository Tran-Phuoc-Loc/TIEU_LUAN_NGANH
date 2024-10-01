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

        // Validate dữ liệu bao gồm cả danh mục và ảnh
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'status' => 'required|in:draft,published',
            'category_id' => 'required|exists:categories,id', // Kiểm tra danh mục có tồn tại
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Kiểm tra định dạng ảnh và kích thước
        ]);

        // Cập nhật bài viết với dữ liệu từ form
        $post->title = $validatedData['title'];
        $post->content = $validatedData['content'];
        $post->status = $validatedData['status'];
        $post->category_id = $validatedData['category_id'];

        // Nếu có file ảnh được tải lên
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($post->image_url) {
                Storage::disk('public')->delete($post->image_url); // Xóa ảnh cũ từ storage
            }

            // Lưu ảnh mới
            $path = $request->file('image')->store('images', 'public');
            $post->image_url = $path; // Lưu đường dẫn ảnh vào DB
        }

        // Lưu các thay đổi
        $post->save();

        // Gửi thông báo cho tác giả bài viết nếu người chỉnh sửa không phải tác giả (admin sửa bài viết của user)
        if (auth()->user()->id !== $post->user_id) {
            $user = $post->user; // Sử dụng quan hệ tác giả
            if ($user) {
                $user->notify(new PostUpdated($post));
            }
        }

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
