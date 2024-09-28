<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
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
        // dd($request->all());
        try {
            $userId = Auth::id();

            $slug = Str::slug($request->input('title')); // Tạo slug từ tiêu đề

            // Lấy giá trị status từ request
            $status = $request->input('status'); // Thay đổi ở đây

            $post = Post::create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'user_id' => $userId,
                'status' => $status, // Sử dụng giá trị đã lấy từ request
                'category_id' => $request->input('category_id'),
                'slug' => $slug,
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
        $post = Post::find($id);
        $categories = Category::all(); // Lấy tất cả danh mục
        // Kiểm tra quyền truy cập
        if (Auth::id() !== $post->user->id) {
            // Chuyển hướng lại với thông báo lỗi nếu họ không sở hữu bài đăng
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền chỉnh sửa bài viết này.');
        }
        if (!$post) {
            return redirect()->route('posts.index')->with('error', 'Bài viết không tồn tại.');
        }

        // Nếu người dùng có quyền, hiển thị trang chỉnh sửa bài viết
        return view('users.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post, $id)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|string|in:draft,published',
            'category_id' => 'required|array', // Cần phải là mảng
            'category_id.*' => 'exists:categories,id', // Kiểm tra danh mục có tồn tại
        ]);

        // Tìm bài viết và cập nhật
        $post = Post::findOrFail($id);
        $post->title = $validatedData['title'];
        $post->content = $validatedData['content'];
        $post->status = $validatedData['status'];

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
        // Cập nhật mối quan hệ với categories
        $post->categories()->sync($validatedData['category_id']); // Đồng bộ các category_id

        return redirect()->route('users.index')->with('success', 'Bài viết đã được cập nhật.');
    }

    public function drafts()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập để tạo bài viết.');
        }

        // Lấy ID của người dùng đã đăng nhập bằng Auth facade
        $userId = Auth::id();

        // Tìm bản nháp cho người dùng hiện tại
        $drafts = Post::with('user')
            ->where('status', 'draft')
            ->where('user_id', $userId)
            ->paginate(10);

        // Hiển thị drafts
        return view('users.posts.drafts', compact('drafts'));
    }

    public function published(User $user)
    {
        // Kiểm tra người dùng đã đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập để xem bài viết.');
        }

        // Lấy tất cả bài viết đã xuất bản thuộc về người dùng hiện tại
        $posts = Post::where('status', 'published')
            ->where('user_id', Auth::id()) // Chỉ lấy bài viết của người dùng hiện tại
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Sử dụng paginate nếu cần phân trang
        // Log::info('Published Posts:', $posts->toArray());
        // Log::info('User ID:', ['id' => $user->id]);
        // Log::info('Generated URL:', ['url' => route('users.posts.published', $user->id)]);



        return view('users.posts.published', compact('posts'));
    }

    public function publish(Request $request, $id)
    {
        Log::info($request->all());
        // Tìm bài viết theo ID
        $post = Post::findOrFail($id);
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
        $post = Post::findOrFail($id);
        // Kiểm tra quyền truy cập
        if (Auth::id() !== $post->user->id) {
            abort(403, 'Unauthorized action.');
        }

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
        // Khởi tạo truy vấn
        $posts = Post::where('status', 'published'); // Chỉ lấy bài viết đã xuất bản

        // Nếu có truy vấn tìm kiếm
        if ($query) {
            $posts = $posts->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%");
            });
        }

        // Lấy danh sách bài viết phù hợp với truy vấn
        $posts = $posts->get();

        // Khởi tạo truy vấn tìm kiếm cho người dùng
        $users = User::query();

        // Nếu có truy vấn tìm kiếm cho người dùng
        if ($query) {
            $users = $users->where(function ($q) use ($query) {
                $q->where('username', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            });
        }

        // Loại bỏ tài khoản với role là admin
        $users = $users->where('role', '!=', 'admin');

        $users = $users->orderByRaw("CASE 
        WHEN username LIKE '{$query}%' THEN 1 
        ELSE 2 
    END")
            ->limit(10)
            ->get();


        // Đảm bảo biến users tồn tại kể cả khi không có truy vấn
        if ($users->isEmpty()) {
            $users = collect([]); // Trả về một collection trống
        }

        // Trả về view với cả bài viết và người dùng
        return view('users.posts.index', compact('posts', 'users', 'query'));
    }


    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail(); // Tìm bài viết bằng slug
        return view('posts.show', compact('post'));
    }
}
