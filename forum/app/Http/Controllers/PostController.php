<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use App\Models\Group;
use App\Models\Product;
use App\Models\Folder;
use App\Models\ForumPost;
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

    protected $groups;

    public function __construct()
    {
        // Lấy tất cả nhóm từ database và gán vào biến groups
        $this->groups = Group::all();
    }

    // Tìm kiếm bài viết
    public function index(Request $request)
    {
        $query = $request->input('query');

        // Kiểm tra nếu người dùng không nhập gì
        if (empty($query)) {
            return redirect()->route('users.index')->with('error', 'Vui lòng nhập ký tự để tìm kiếm.');
        }

        // Tìm kiếm bài viết diễn đàn
        $forumPostsQuery = ForumPost::with('user');

        // Nếu có truy vấn tìm kiếm cho diễn đàn
        if ($query) {
            $forumPostsQuery->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%");
            });
        }

        // Lấy danh sách bài viết diễn đàn sau khi thêm điều kiện tìm kiếm
        $forumPosts = $forumPostsQuery->get();

        // Khởi tạo truy vấn cho bài viết mạng xã hội
        $postsQuery = Post::where('status', 'published')->with(['user', 'category']);

        if ($query) {
            $postsQuery->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%");
            });
        }
        $posts = $postsQuery->get();

        // Khởi tạo truy vấn tìm kiếm cho người dùng
        $usersQuery = User::query();
        if ($query) {
            $usersQuery->where(function ($q) use ($query) {
                $q->where('username', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            });
        }
        $users = $usersQuery->where('role', '!=', 'admin')
            ->orderByRaw("CASE 
                WHEN username LIKE '{$query}%' THEN 1 
                ELSE 2 
           END")
            ->limit(10)
            ->get();

        // Khởi tạo truy vấn tìm kiếm cho nhóm
        $groupsQuery = Group::query();
        if ($query) {
            $groupsQuery->where('name', 'LIKE', "%{$query}%");
        }
        $groups = $groupsQuery->get();

        // Khởi tạo truy vấn tìm kiếm cho sản phẩm
        $productsQuery = Product::query();
        if ($query) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            });
        }
        $products = $productsQuery->get();


        // Trả về view với cả bài viết, bài viết diễn đàn, người dùng và nhóm
        return view('users.posts.index', compact('forumPosts', 'posts', 'users', 'groups', 'query', 'products'));
    }

    public function create(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập để tạo bài viết.');
        }

        // Lấy groupId từ query string
        $groupId = $request->query('groupId');
        $group = null;

        if ($groupId) {
            $group = Group::findOrFail($groupId);
        }

        $categories = Category::all();
        // dd($groupId);

        return view('users.posts.create', compact('categories', 'group'));
    }

    public function store(StorePostRequest $request)
    {
        try {
            // Lấy ID người dùng đã đăng nhập
            $userId = Auth::id();

            // Tạo bài viết mới
            $post = Post::create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'group_id' => $request->input('group_id'),
                'user_id' => $userId,
                'category_id' => $request->input('category_id'),
                'status' => $request->input('status'),
                'slug' => Str::slug($request->input('title')),
            ]);

            // Nếu có group_id, bài viết thuộc nhóm
            if ($request->group_id) {
                $post->group_id = $request->group_id;
            }

            // **Xử lý tệp đơn (ảnh hoặc video)**
            if ($request->hasFile('media_single')) {
                $mediaSingle = $request->file('media_single');

                // Kiểm tra kích thước tệp (giới hạn 5MB cho ảnh, 50MB cho video)
                if (in_array($mediaSingle->getMimeType(), ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'])) {
                    if ($mediaSingle->getSize() > 50 * 1024 * 1024) {
                        Log::error('Video quá lớn: ' . $mediaSingle->getSize());
                        return redirect()->route('users.posts.create')->with('error', 'Video quá lớn. Kích thước tối đa là 50MB.');
                    }
                } else {
                    if ($mediaSingle->getSize() > 5 * 1024 * 1024) {
                        Log::error('Ảnh quá lớn: ' . $mediaSingle->getSize());
                        return redirect()->route('users.posts.create')->with('error', 'Ảnh quá lớn. Kích thước tối đa là 5MB.');
                    }
                }

                // Kiểm tra xem tệp tải lên có phải là video không
                if (in_array($mediaSingle->getMimeType(), ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'])) {
                    // Lưu video vào thư mục uploads/
                    $filename = time() . '_' . $mediaSingle->getClientOriginalName();
                    $filePath = $mediaSingle->storeAs('public/uploads', $filename, 'public');
                    $post->image_url = 'uploads/' . $filename;
                } else {
                    // Nếu là ảnh, lưu vào thư mục image/
                    $filename = time() . '_' . $mediaSingle->getClientOriginalName();
                    $filePath = $mediaSingle->storeAs('image', $filename, 'public');
                    $post->image_url = 'image/' . $filename;
                }
            }

            // **Xử lý upload nhiều ảnh (chỉ khi media_single không phải video)**
            if ($request->hasFile('media_multiple') && (!$request->hasFile('media_single') || !in_array($request->file('media_single')->getMimeType(), ['video/mp4', 'video/avi', 'video/mov', 'video/mkv']))) {
                foreach ($request->file('media_multiple') as $file) {

                    // Kiểm tra kích thước tệp (giới hạn 5MB)
                    if ($file->getSize() > 5 * 1024 * 1024) {  // 5MB
                        return redirect()->route('users.posts.create')->with('error', 'Một số ảnh bạn tải lên quá lớn, vui lòng thử lại với ảnh nhỏ hơn 5MB.');
                    }

                    // Kiểm tra loại tệp ảnh
                    if (in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('uploads', $filename, 'public');

                        // Lưu thông tin ảnh vào bảng PostImage
                        PostImage::create([
                            'post_id' => $post->id,
                            'file_path' => 'uploads/' . $filename,
                        ]);
                    }
                }
            }

            // Lưu thông tin bài viết nếu chưa được lưu trước đó
            $post->save();

            // Chuyển hướng sau khi lưu bài viết thành công
            return redirect()->route('users.index')->withErrors('success', 'Bài viết đã được lưu thành công.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo bài viết: ' . $e->getMessage());
            return redirect()->route('users.posts.create')->withErrors('error', 'Có lỗi xảy ra khi lưu bài viết.');
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

        // Lấy tất cả nhóm 
        $groups = Group::all();

        // Nếu người dùng có quyền, hiển thị trang chỉnh sửa bài viết
        return view('users.posts.edit', compact('post', 'categories', 'groups'));
    }

    public function update(StorePostRequest $request, Post $post)
    {
        try {

            // Lấy dữ liệu đã được xác thực từ yêu cầu
            $validatedData = $request->validated();

            // Cập nhật bài viết
            $post->update([
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
                'category_id' => $validatedData['category_id'],
                'status' => $validatedData['status'],
                'slug' => Str::slug($validatedData['title']),
            ]);

            // Cập nhật `group_id` nếu có trong yêu cầu
            if ($request->has('group_id')) {
                $post->group_id = $request->input('group_id');
            }

            // Nếu trạng thái là "published", cập nhật thời gian published_at
            if ($validatedData['status'] === 'published') {
                $post->published_at = now();
            } else {
                $post->published_at = null;
            }

            // **Xử lý upload tệp đơn (ảnh hoặc video)**
            if ($request->hasFile('media_single')) {
                $file = $request->file('media_single');
                $isVideo = str_contains($file->getMimeType(), 'video');

                // Xóa file cũ nếu tồn tại
                if ($post->image_url) {
                    Storage::disk('public')->delete($post->image_url);
                }

                // Lưu file mới
                $filename = time() . '_' . $file->getClientOriginalName();

                if ($isVideo) {
                    // Nếu là video, lưu vào thư mục uploads
                    $filePath = $file->storeAs('uploads', $filename, 'public');
                    $post->image_url = 'uploads/' . $filename;

                    // Xóa tất cả các ảnh phụ nếu có
                    PostImage::where('post_id', $post->id)->delete();
                } else {
                    // Nếu là ảnh, lưu vào thư mục image
                    $filePath = $file->storeAs('image', $filename, 'public');
                    $post->image_url = 'image/' . $filename;
                }
            }

            // **Xử lý upload nhiều ảnh (chỉ khi media_single không phải video)**
            if ($request->hasFile('media_multiple') && (!$request->hasFile('media_single') || !str_contains($request->file('media_single')->getMimeType(), 'video'))) {
                // Xóa các ảnh phụ cũ trước khi cập nhật ảnh mới
                PostImage::where('post_id', $post->id)->delete();

                foreach ($request->file('media_multiple') as $file) {
                    if (str_contains($file->getMimeType(), 'image')) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('uploads', $filename, 'public');

                        PostImage::create([
                            'post_id' => $post->id,
                            'file_path' => 'uploads/' . $filename,
                        ]);
                    }
                }
            }

            // Lưu thay đổi bài viết
            $post->save();

            return redirect()->route('users.index')->with('success', 'Bài viết đã được cập nhật thành công.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật bài viết: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật bài viết.');
        }
    }

    public function drafts()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập để tạo bài viết.');
        }

        // Lấy ID của người dùng đã đăng nhập bằng Auth facade
        $userId = Auth::id();

        $groups = Group::all();

        // Tìm bản nháp cho người dùng hiện tại và lấy dữ liệu
        $drafts = Post::with('user')
            ->where('status', 'draft')
            ->where('user_id', $userId)
            ->get(); // Lấy dữ liệu từ cơ sở dữ liệu

        // Hiển thị drafts
        return view('users.posts.drafts', compact('drafts', 'groups'));
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

        // Lấy các thư mục của người dùng
        $folders = Folder::where('user_id', $userId)->get();

        // Truyền vào view dưới dạng mảng
        return view('users.posts.published', [
            'published' => $published,
            'isCurrentUser' => $isCurrentUser,
            'folders' => $folders, // Thêm biến $folders vào view
            'groups' => $this->groups
        ]);
    }

    // bỏ
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

        return redirect()->route('users.index')->with('success', 'Bài viết đã được xóa.');
    }

    // Thu hồi bài viết
    public function recall($id)
    {
        $post = Post::findOrFail($id);

        // Chuyển bài viết về trạng thái draft
        $post->status = 'draft';
        $post->save();

        return redirect()->route('users.posts.draft')->with('success', 'Bài viết đã được thu hồi về nháp.');
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

    public function show($id)
    {
        $post = Post::with(['user', 'category'])->findOrFail($id);
        $groups = Group::all();
        // Kiểm tra xem người dùng có quyền truy cập bài viết
        if (Auth::id() !== $post->user_id) {
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền truy cập hoặc chủ bài viết này đã xóa.');
        }

        return view('users.posts.show', compact('post', 'groups')); // Trả về view để hiển thị bài viết
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

    public function showFolderSelection($folderId)
    {
        // Lấy thư mục theo folderId và kiểm tra quyền sở hữu
        $folder = Folder::with('savedPosts')->findOrFail($folderId);

        // Kiểm tra nếu thư mục không thuộc về người dùng hiện tại
        if ($folder->user_id !== Auth::id()) {
            // Nếu không phải chủ sở hữu, redirect về trang trước đó hoặc trả về lỗi
            return redirect()->route('some.route')->with('error', 'Bạn không có quyền truy cập thư mục này.');
        }

        // Lấy các thư mục và bài viết đã lưu của người dùng
        $folders = Folder::with('savedPosts')
            ->where('user_id', Auth::id()) // Chỉ lấy thư mục của người dùng hiện tại
            ->get();

        // Lấy tất cả nhóm (có thể sẽ sử dụng trong view)
        $groups = Group::all();

        // Trả về view và truyền dữ liệu
        return view('users.posts.savePost', compact('folders', 'groups', 'folder'));
    }

    public function showSavedPosts()
    {
        // Lấy tất cả các thư mục của người dùng hiện tại
        $folders = Folder::where('user_id', Auth::id())->with('posts')->get(); // Gắn quan hệ bài viết trong mỗi thư mục

        $groups = Group::all(); // Lấy nhóm nếu cần

        // Truyền dữ liệu vào view
        return view('users.posts.selectFolder', compact('folders', 'groups'));
    }

    // Xử lý việc hiển thị bài viết trong một thư mục
    public function showPostsByFolder(Folder $folder)
    {
        // Lấy người dùng hiện tại
        $user = Auth::user();

        // Lấy danh sách người dùng gợi ý theo dõi, lấy 5 người ngẫu nhiên
        $usersToFollow = User::where('role', 'user')
            ->where('id', '!=', Auth::id()) // Loại bỏ người dùng hiện tại
            ->inRandomOrder()
            ->take(5)
            ->get();

        // Lấy tất cả bài viết trong thư mục, kèm thông tin người tạo bài viết
        $posts = $folder->posts()->with('user')->get();

        // Lấy danh sách ID bài viết đã lưu nếu người dùng đã đăng nhập
        $savedPosts = [];
        if ($user) {
            $savedPosts = SavedPost::where('user_id', $user->id)->pluck('post_id')->toArray();
        }

        // Truyền dữ liệu vào view
        return view('users.index', compact('posts', 'folder', 'usersToFollow', 'savedPosts'));
    }
}
