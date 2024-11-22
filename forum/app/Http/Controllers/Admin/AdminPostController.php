<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Group;
use App\Models\PostImage;
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

        // Lọc theo ngày tạo bài viết (theo khoảng thời gian)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $query->whereBetween('created_at', [$startDate, $endDate]);
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
        // Lấy danh sách tất cả các danh mục
        $categories = Category::all();
        $groups = Group::all();         // Lấy tất cả nhóm
        return view('admin.posts.edit', compact('post', 'categories', 'groups'));
    }

    // Cập nhật bài viết
    public function update(Request $request, $id)
    {
        // Lấy bài viết theo ID
        $post = Post::findOrFail($id);

        // Kiểm tra nếu có thay đổi về lý do sửa bài viết
        if ($request->has('edit_reason') && $request->edit_reason !== $post->edit_reason) {
            session()->flash('edit_reason_changed', true);
        }
        
        // Lưu đường dẫn cũ để xóa nếu có
        $oldImageUrl = $post->image_url;
        $oldImages = $post->images; // Mỗi bài viết có nhiều ảnh (dùng quan hệ với PostImage)

        // Validate dữ liệu
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'edit_reason' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'group_id' => 'nullable|exists:groups,id', // Kiểm tra nhóm hợp lệ
        ]);

        // Cập nhật bài viết với dữ liệu từ form
        $post->title = $validatedData['title'];
        $post->content = $validatedData['content'];
        $post->edit_reason = $validatedData['edit_reason'];
        $post->status = $validatedData['status'];
        $post->category_id = $validatedData['category_id'];
        $post->group_id = $validatedData['group_id'];

        // **Xử lý tệp đơn (ảnh hoặc video)**
        if ($request->hasFile('media_single')) {
            // Nếu có tệp cũ, xóa nó
            if ($oldImageUrl) {
                $oldImagePath = public_path('storage/' . $oldImageUrl);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $mediaSingle = $request->file('media_single');

            // Kiểm tra kích thước tệp (giới hạn 5MB cho ảnh, 50MB cho video)
            if (in_array($mediaSingle->getMimeType(), ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'])) {
                if ($mediaSingle->getSize() > 50 * 1024 * 1024) {
                    return redirect()->route('users.posts.create')->with('error', 'Video quá lớn. Kích thước tối đa là 50MB.');
                }
            } else {
                if ($mediaSingle->getSize() > 5 * 1024 * 1024) {
                    return redirect()->route('users.posts.create')->with('error', 'Ảnh quá lớn. Kích thước tối đa là 5MB.');
                }
            }

            // Kiểm tra xem tệp tải lên có phải là video không
            if (in_array($mediaSingle->getMimeType(), ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'])) {
                $filename = time() . '_' . $mediaSingle->getClientOriginalName();
                $filePath = $mediaSingle->storeAs('public/uploads', $filename, 'public');
                $post->image_url = 'uploads/' . $filename;
            } else {
                $filename = time() . '_' . $mediaSingle->getClientOriginalName();
                $filePath = $mediaSingle->storeAs('image', $filename, 'public');
                $post->image_url = 'image/' . $filename;
            }
        }

        // **Xử lý upload nhiều ảnh (chỉ khi media_single không phải video)**
        if ($request->hasFile('media_multiple') && (!$request->hasFile('media_single') || !in_array($request->file('media_single')->getMimeType(), ['video/mp4', 'video/avi', 'video/mov', 'video/mkv']))) {
            // Duyệt qua các ảnh mới
            foreach ($request->file('media_multiple') as $file) {
                if ($file->getSize() > 5 * 1024 * 1024) {  // 5MB
                    return redirect()->route('users.posts.create')->with('error', 'Một số ảnh bạn tải lên quá lớn, vui lòng thử lại với ảnh nhỏ hơn 5MB.');
                }

                if (in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads', $filename, 'public');

                    // Thêm ảnh mới vào cơ sở dữ liệu
                    PostImage::create([
                        'post_id' => $post->id,
                        'file_path' => 'uploads/' . $filename,
                    ]);
                }
            }
        }

        // Kiểm tra nếu có ảnh đã được xóa khỏi bài viết
        if ($request->has('removed_images')) {
            $removedImages = json_decode($request->input('removed_images'), true);

            // Xóa ảnh đã bị xóa
            foreach ($removedImages as $imageId) {
                $image = PostImage::find($imageId);
                if ($image) {
                    $imagePath = public_path('storage/' . $image->file_path);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $image->delete();
                }
            }
        }

        // Gửi thông báo đến người dùng
        $user = User::find($post->user_id);
        if ($user) {
            $user->notify(new PostUpdated($post));
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
