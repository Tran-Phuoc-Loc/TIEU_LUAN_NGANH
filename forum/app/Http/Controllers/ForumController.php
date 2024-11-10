<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index()
    {
        // Lấy 5 danh mục và 5 bài viết mới nhất trong mỗi danh mục
        $categories = ForumCategory::with(['posts' => function ($query) {
            $query->latest()->take(5);
        }])->take(5)->get();

        // Lấy 5 bài viết mới nhất từ tất cả các bài viết
        $latestPosts = ForumPost::with('user')->latest()->take(5)->get();

        // Lấy tất cả bài viết để hiển thị trong nội dung chính
        $posts = ForumPost::with('user')->latest()->get();

        // Lấy tất cả nhóm
        $groups = Group::all();

        return view('users.forums.index', compact('categories', 'posts', 'latestPosts', 'groups'));
    }

    public function show($id)
    {
        // Lấy 5 danh mục có nhiều bài viết nhất
        $categories = ForumCategory::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(5)
            ->with('posts.user')
            ->get();

        // Tải bài viết chi tiết cùng với thông tin người dùng và các bình luận
        $forumPost = ForumPost::with('user', 'comments.user')->findOrFail($id);

        // Lấy tất cả nhóm
        $groups = Group::all();

        return view('users.forums.show', compact('categories', 'forumPost', 'groups'));
    }

    // Hiển thị form tạo bài viết
    public function create()
    {
        $categories = ForumCategory::all();
        // Lấy tất cả nhóm 
        $groups = Group::all();
        return view('users.forums.create', compact('categories', 'groups'));
    }

    // Lưu bài viết mới vào cơ sở dữ liệu
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'forum_category_id' => 'required|exists:forum_categories,id', // Thêm dòng này
        ]);

        ForumPost::create([
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'forum_category_id' => $validatedData['forum_category_id'], // Đảm bảo có giá trị này
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('forums.index')->with('success', 'Bài viết đã được tạo!');
    }

    // Phương thức chỉnh sửa bài viết
    public function edit($id)
    {
        // Tìm bài viết theo ID
        $post = ForumPost::findOrFail($id);

        // Kiểm tra nếu người dùng hiện tại là chủ sở hữu bài viết hoặc có quyền admin
        if (auth::user()->id !== $post->user_id && auth::user()->role !== 'admin') {
            return redirect()->route('forums.index')->with('error', 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        // Lấy danh sách các danh mục
        $categories = ForumCategory::all();

        // Trả về view chỉnh sửa với dữ liệu bài viết
        return view('forums.edit', compact('post', 'categories'));
    }

    public function update(Request $request, $id)
    {
        // Tìm bài viết theo ID
        $post = ForumPost::findOrFail($id);

        // Kiểm tra nếu người dùng hiện tại là chủ sở hữu bài viết hoặc có quyền admin
        if (auth::user()->id !== $post->user_id && auth::user()->role !== 'admin') {
            return redirect()->route('forums.index')->with('error', 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'forum_category_id' => 'required|exists:forum_categories,id', // Đảm bảo trường danh mục tồn tại
        ]);

        // Cập nhật nội dung bài viết
        $post->update([
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'forum_category_id' => $validatedData['forum_category_id'], // Đảm bảo cập nhật danh mục
        ]);

        return redirect()->route('forums.show', $post->id)->with('success', 'Bài viết đã được cập nhật thành công.');
    }

    public function showCategory($id)
    {
        // Hiển thị bài viết trong một danh mục
        $category = ForumCategory::with('posts')->findOrFail($id);
        return view('users.forums.category', compact('category'));
    }

    // Phương thức xóa bài viết
    public function destroy($id)
    {
        // Tìm bài viết theo ID
        $post = ForumPost::findOrFail($id);

        // Kiểm tra nếu người dùng hiện tại là chủ sở hữu bài viết hoặc có quyền admin
        if (auth::user()->id !== $post->user_id && auth::user()->role !== 'admin') {
            return redirect()->route('forums.index')->with('error', 'Bạn không có quyền xóa bài viết này.');
        }

        // Thực hiện xóa bài viết
        $post->delete();

        // Chuyển hướng về trang danh sách với thông báo thành công
        return redirect()->route('forums.index')->with('success', 'Bài viết đã được xóa thành công.');
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $filename, 'public');

            $url = asset('storage/' . $filePath);
            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'Tải ảnh thất bại'], 400);
    }
}
