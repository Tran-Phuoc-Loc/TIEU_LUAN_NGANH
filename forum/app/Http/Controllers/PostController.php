<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use App\Models\Folder;
use App\Models\Category;
use App\Notifications\PostUpdated;
use App\Models\SavedPost;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập để tạo bài viết.');
        }
        $categories = Category::all(); // Lấy tất cả danh mục
        return view('users.posts.create', compact('categories')); // Hiển thị trang tạo bài viết
    }

    public function store(StorePostRequest $request)
    {
        try {
            $userId = Auth::id();

            // Tạo bài viết
            $post = Post::create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'user_id' => $userId,
                'category_id' => $request->input('category_id'),
                'status' => $request->input('status'),
                'slug' => Str::slug($request->input('title')),
            ]);

            // Xử lý file upload nếu có
            if ($request->hasFile('image')) {
                $filename = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('public/images/', $filename);
                $post->update(['image_url' => 'images/' . $filename]);
            }

            return redirect()->route('users.posts.create')->with('success', 'Bài viết đã lưu.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo bài viết: ' . $e->getMessage());
            return redirect()->route('users.posts.create')->with('error', 'Có lỗi xảy ra khi lưu bài viết.');
        }
    }

    public function edit($id)
    {
        // Lấy bài viết theo ID
        $post = Post::find($id);

        // Kiểm tra xem bài viết có tồn tại không
        if (!$post) {
            return redirect()->route('users.index')->with('error', 'Bài viết không tồn tại.');
        }

        // Kiểm tra quyền truy cập
        if (Auth::id() !== $post->user_id) {
            // Chuyển hướng lại với thông báo lỗi nếu họ không sở hữu bài đăng
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        $categories = Category::all(); // Lấy tất cả danh mục

        // Nếu người dùng có quyền, hiển thị trang chỉnh sửa bài viết
        return view('users.posts.edit', compact('post', 'categories'));
    }


    public function update(Request $request, Post $post)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|string|in:draft,published',
            'category_id' => 'required|exists:categories,id', // Kiểm tra danh mục có tồn tại
        ]);

        // Tìm bài viết và cập nhật
        $post->title = $validatedData['title'];
        $post->content = $validatedData['content'];
        $post->status = $validatedData['status'];
        $post->category_id = $validatedData['category_id'];

        // Tạo slug từ tiêu đề
        $post->slug = Str::slug($validatedData['title']); // Cập nhật slug từ tiêu đề

        // Nếu trạng thái là published, cập nhật thời gian published_at
        if ($post->status === 'published') {
            $post->published_at = now(); // Reset thời gian khi được công bố
        } else {
            $post->published_at = null; // Reset nếu quay về draft
        }

        // Xử lý upload ảnh nếu có
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu cần
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            // Lưu ảnh mới
            $path = $request->file('image')->store('images', 'public');
            $post->image_url = $path;
        }

        $post->save();

        return redirect()->route('users.index')->with('success', 'Bài viết đã được cập nhật.');
    }

    public function drafts()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập để tạo bài viết.');
        }

        // Lấy ID của người dùng đã đăng nhập bằng Auth facade
        $userId = Auth::id();

        // Tìm bản nháp cho người dùng hiện tại và lấy dữ liệu
        $drafts = Post::with('user')
            ->where('status', 'draft')
            ->where('user_id', $userId)
            ->get(); // Lấy dữ liệu từ cơ sở dữ liệu

        // Hiển thị drafts
        return view('users.posts.drafts', compact('drafts'));
    }

    public function published($userId = null)
    {
        // Nếu không truyền ID người dùng, giả định rằng người dùng đang xem bài viết của chính họ
        $currentUserId = Auth::id();

        // Nếu người dùng chưa đăng nhập, chuyển hướng đến trang đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập để xem bài viết.');
        }

        // Nếu $userId là null, gán nó bằng ID của người dùng đang đăng nhập
        $userId = $userId ?? $currentUserId;

        // Lấy các bài viết đã xuất bản của người dùng
        $published = Post::with('user')
            ->where('status', 'published')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Xác định xem người dùng đang xem bài viết của mình hay của người khác
        $isCurrentUser = $currentUserId === (int) $userId;

        return view('users.posts.published', compact('published', 'isCurrentUser'));
    }

    public function publish(Request $request, $id)
    {
        Log::info($request->all());
        // Tìm bài viết theo ID
        $post = Post::find($id);
        // Xác thực dữ liệu đầu vào, nếu cần
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Cập nhật thông tin bài viết
        $post->title = $validatedData['title'];
        $post->content = $validatedData['content'];


        // Xử lý upload ảnh nếu có
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            // Lưu ảnh mới
            $path = $request->file('image')->store('images', 'public');
            $post->image_url = $path; // Lưu đường dẫn ảnh vào cơ sở dữ liệu
        }

        $post->status = 'published'; // Đặt trạng thái là 'published'
        $post->save();

        // Chuyển hướng hoặc trả về phản hồi
        return redirect()->route('users.index')->with('success', 'Bài viết đã được xuất bản!');
    }


    public function destroy($id)
    {
        // Tìm bài viết theo ID
        $post = Post::findOrFail($id);

        // Kiểm tra quyền truy cập
        if (Auth::id() !== $post->user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Xóa tất cả bình luận liên quan đến bài viết
        $post->comments()->delete(); // Giả sử bạn đã định nghĩa mối quan hệ comments trong model Post

        // Xóa bài viết
        $post->delete();

        return redirect()->route('users.posts.drafts')->with('success', 'Bài viết đã được xóa.');
    }

    // Thu hồi bài viết
    public function recall($id)
    {
        $post = Post::findOrFail($id);

        // Chuyển bài viết về trạng thái draft
        $post->status = 'draft';
        $post->save();

        return redirect()->route('users.posts.published')->with('success', 'Bài viết đã được thu hồi về nháp.');
    }

    public function like($id)
    {
        try {
            // Kiểm tra xem người dùng đã đăng nhập chưa
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập.'], 401);
            }

            // Tìm bài viết theo ID hoặc trả về lỗi 404 nếu không tồn tại
            $post = Post::findOrFail($id);

            // Kiểm tra xem người dùng đã thích bài viết chưa
            $like = $post->likes()->where('user_id', Auth::id())->first();

            if ($like) {
                // Nếu đã thích, xóa lượt thích
                $like->delete();
                $post->decrement('likes_count'); // Giảm số lượng lượt thích
                $isLiked = false;
            } else {
                // Nếu chưa thích, thêm lượt thích mới
                $post->likes()->create(['user_id' => Auth::id()]); // Chỉ cần cung cấp user_id
                $post->increment('likes_count'); // Tăng số lượng lượt thích
                $isLiked = true;
            }
            // Lấy số lượt thích mới từ CSDL
            $post->refresh(); // Làm mới dữ liệu từ CSDL
            $post->save();
            return response()->json([
                'success' => true,
                'isLiked' => $isLiked,
                'new_like_count' => $post->likes_count, // Trả về số lượt thích mới
            ]);
        } catch (\Exception $e) {
            // Trả về phản hồi lỗi nếu có vấn đề
            return response()->json(['success' => false, 'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại.'], 500);
        }
    }

    public function index(Request $request)
    {
        $query = $request->input('query');

        // Khởi tạo truy vấn cho bài viết
        $postsQuery = Post::where('status', 'published')->with(['user', 'category']);

        // Nếu có truy vấn tìm kiếm
        if ($query) {
            $postsQuery->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%");
            });
        }

        // Lấy danh sách bài viết sau khi thêm điều kiện tìm kiếm
        $posts = $postsQuery->get();

        // Khởi tạo truy vấn tìm kiếm cho người dùng
        $usersQuery = User::query();

        // Nếu có truy vấn tìm kiếm cho người dùng
        if ($query) {
            $usersQuery->where(function ($q) use ($query) {
                $q->where('username', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            });
        }

        // Loại bỏ tài khoản với role là admin
        $users = $usersQuery->where('role', '!=', 'admin')
            ->orderByRaw("CASE 
                    WHEN username LIKE '{$query}%' THEN 1 
                    ELSE 2 
               END")
            ->limit(10)
            ->get();

        // Khởi tạo truy vấn tìm kiếm cho nhóm
        $groupsQuery = Group::query();

        // Nếu có truy vấn tìm kiếm cho nhóm
        if ($query) {
            $groupsQuery->where('name', 'LIKE', "%{$query}%");
        }

        // Lấy danh sách nhóm phù hợp với truy vấn
        $groups = $groupsQuery->get();

        // Trả về view với cả bài viết, người dùng và nhóm
        return view('users.posts.index', compact('posts', 'users', 'groups', 'query'));
    }

    public function show($id)
    {
        $post = Post::with(['user', 'category'])->findOrFail($id);

        // Kiểm tra xem người dùng có quyền truy cập bài viết
        if (Auth::id() !== $post->user_id) {
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền truy cập bài viết này.');
        }

        return view('users.posts.show', compact('post')); // Trả về view để hiển thị bài viết
    }

    public function savePost(Request $request)
    {
        $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
            'folder_id' => 'required|integer|exists:folders,id', // Thêm kiểm tra cho thư mục
        ]);

        $userId = Auth::id();

        // Kiểm tra xem bài viết đã được lưu chưa
        if (SavedPost::where('user_id', $userId)->where('post_id', $request->post_id)->where('folder_id', $request->folder_id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Bài viết đã được lưu trong thư mục này.']);
        }

        SavedPost::create([
            'user_id' => $userId,
            'post_id' => $request->post_id,
            'folder_id' => $request->folder_id, // Lưu ID của thư mục vào bảng saved_posts
        ]);

        return response()->json(['success' => true, 'message' => 'Bài viết đã được lưu thành công!']);
    }

    public function showSavedPosts()
    {
        // Tải các thư mục kèm theo bài viết đã lưu trong từng thư mục
        $folders = Folder::with('savedPosts.post')
            ->where('user_id', Auth::id())
            ->get();

        return view('users.posts.savePost', compact('folders'));
    }
}
