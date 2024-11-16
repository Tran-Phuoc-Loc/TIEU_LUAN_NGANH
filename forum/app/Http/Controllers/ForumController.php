<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        // Kiểm tra nếu có tham số 'id' trong URL
        if ($request->has('id')) {
            // Lọc bài viết theo id nếu có tham số 'id'
            $posts = ForumPost::with('user')
                ->where('id', $request->id) // Lọc bài viết theo id
                ->latest()
                ->get();
        } else {
            // Nếu không có id, lấy tất cả bài viết
            $posts = ForumPost::with('user')->latest()->get();
        }

        // Lấy 5 danh mục và 5 bài viết mới nhất trong mỗi danh mục
        $categories = ForumCategory::with(['posts' => function ($query) {
            $query->latest()->take(5);
        }])->take(5)->get();

        // Lấy 5 bài viết mới nhất từ tất cả các bài viết
        $latestPosts = ForumPost::with('user')->latest()->take(5)->get();

        // Lấy tất cả nhóm
        $groups = Group::all();

        // Trả về view với các biến cần thiết
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

        // Lấy 5 bài viết mới nhất từ tất cả các bài viết
        $latestPosts = ForumPost::with('user')->latest()->take(5)->get();

        // Tải bài viết chi tiết cùng với thông tin người dùng và các bình luận
        $forumPost = ForumPost::with('user', 'comments.user')->findOrFail($id);

        // Lấy tất cả nhóm
        $groups = Group::all();

        return view('users.forums.show', compact('categories', 'forumPost', 'groups', 'latestPosts'));
    }

    // Hiển thị form tạo bài viết
    public function create()
    {
        $categories = ForumCategory::all();

        // Lấy tất cả nhóm 
        $groups = Group::all();

        // Lấy 5 bài viết mới nhất từ tất cả các bài viết
        $latestPosts = ForumPost::with('user')->latest()->take(5)->get();

        return view('users.forums.create', compact('categories', 'groups', 'latestPosts'));
    }

    // Lưu bài viết mới vào cơ sở dữ liệu
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'forum_category_id' => 'required|exists:forum_categories,id',
        'file' => 'nullable|file|mimes:pdf,docx,pptx,jpeg,png,jpg|max:2048',  // Kiểm tra tệp tải lên
    ]);

    // Xử lý tải tệp (nếu có)
    $filePath = null;
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        
        // Kiểm tra lại phần mở rộng tệp
        $extension = $file->getClientOriginalExtension();  // Lấy phần mở rộng tệp
        $mimeType = $file->getMimeType();  // Lấy MIME type của tệp

        // Kiểm tra loại tệp tải lên
        if (!in_array($extension, ['pdf', 'docx', 'pptx', 'jpeg', 'png', 'jpg'])) {
            return back()->withErrors(['file' => 'Chỉ hỗ trợ tệp có định dạng pdf, docx, pptx, jpeg, png, jpg.']);
        }

        // Lưu tệp
        $filePath = $file->store('forum_uploads', 'public');
    }

    // Lưu bài viết vào cơ sở dữ liệu
    ForumPost::create([
        'title' => $validatedData['title'],
        'content' => $validatedData['content'],
        'forum_category_id' => $validatedData['forum_category_id'],
        'user_id' => Auth::id(),
        'file_path' => $filePath,  // Lưu đường dẫn tệp
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
            return redirect()->route('users.forums.index')->with('error', 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        // Lấy tất cả nhóm 
        $groups = Group::all();

        // Lấy 5 bài viết mới nhất từ tất cả các bài viết
        $latestPosts = ForumPost::with('user')->latest()->take(5)->get();

        // Lấy danh sách các danh mục
        $categories = ForumCategory::all();

        // Trả về view chỉnh sửa với dữ liệu bài viết
        return view('users.forums.edit', compact('post', 'categories', 'groups', 'latestPosts'));
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

        return redirect()->route('users.forums.show', $post->id)->with('success', 'Bài viết đã được cập nhật thành công.');
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
