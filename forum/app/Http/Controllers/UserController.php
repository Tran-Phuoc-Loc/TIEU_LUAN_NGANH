<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Models\SavedPost;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Group;
use App\Models\Folder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // Trang chủ cho bài viết
    public function index(Request $request)
    {
        // Lấy tất cả người dùng
        $users = User::all();
        $posts = collect();  // Đảm bảo biến $posts luôn tồn tại
        $user = Auth::user();
        $group = Group::find($request->group_id);

        // Khởi tạo biến $post mặc định là null
        $post = null;
        $postFromNotification = null; // Đảm bảo biến luôn tồn tại

        // Kiểm tra nếu có tham số 'post_id' trong URL
        if ($request->has('post_id')) {
            $query = Post::with('user')
                ->withCount('likes', 'comments')
                ->where('status', 'published')
                ->where('id', $request->post_id);

            $post = $query->first();
            // Kiểm tra nếu có bài viết
            if ($post) {
                $posts = collect([$post]); // Chuyển bài viết thành collection nếu tìm thấy
            } else {
                $posts = collect(); // Nếu không có bài viết, trả về collection rỗng
            }
        }
        // Nếu có tham số 'folder_id'
        elseif ($request->has('folder_id') && $user) {
            $folder = Folder::with('savedPosts.post')->find($request->folder_id);

            if ($folder) {
                // Lấy tất cả các bài viết trong thư mục đã lưu của người dùng
                $posts = $folder->savedPosts->map(function ($savedPost) {
                    return $savedPost->post;
                });
            }
        }
        // Nếu có 'group_id'
        elseif ($request->has('group_id') && $user) {
            if ($group && $group->members->contains($user->id)) {
                $query = Post::with(['user', 'group.members'])
                    ->withCount('likes', 'comments')
                    ->where('status', 'published')
                    ->where('group_id', $request->group_id);

                // Áp dụng sorting
                $query = $this->applySorting($query, $request->sort);
                $posts = $query->get();
            } else {
                $posts = collect();
            }
        }

        // Nếu có tham số 'user_posts', chỉ lấy bài viết của người dùng hiện tại
        elseif ($request->has('user_posts') && $user) {
            $query = Post::with(['user', 'group.members'])
                ->withCount('likes', 'comments')
                ->where('user_id', $user->id)
                ->where('status', 'published');

            // Áp dụng sorting nếu có
            $query = $this->applySorting($query, $request->sort);
            $posts = $query->get();
        }
        // Mặc định lấy tất cả bài viết
        else {
            $query = Post::with(['user', 'group.members'])
                ->withCount('likes', 'comments')
                ->where('status', 'published');

            // Áp dụng sorting
            $query = $this->applySorting($query, $request->sort);
            $posts = $query->get();
        }

        // Lấy danh sách người dùng gợi ý theo dõi, lấy 5 người ngẫu nhiên
        $usersToFollow = User::where('role', 'user')
            ->where('id', '!=', Auth::id()) // Loại bỏ người dùng hiện tại
            ->inRandomOrder()
            ->take(5)
            ->get();

        // Khởi tạo các biến thông báo và bài viết
        $unreadNotifications = [];
        $readNotifications = [];
        $folders = [];
        $savedPosts = [];
        $groups = collect();

        if ($user) {
            // Lấy thông báo chưa đọc và đã đọc
            $unreadNotifications = $user->unreadNotifications;
            $readNotifications = $user->notifications()
                ->whereNotNull('read_at')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Lấy thông tin bài viết từ thông báo đầu tiên chưa đọc (nếu có)
            if ($unreadNotifications->isNotEmpty()) {
                $postId = $unreadNotifications->first()->data['post_id'] ?? null;
                $postFromNotification = $postId ? Post::find($postId) : null;
            }

            // Lấy các nhóm đã tham gia và tạo bởi người dùng
            $joinedGroups = $user->groups()->with('creator', 'memberRequests.user')->get();
            $createdGroups = Group::where('creator_id', $user->id)->with('creator', 'memberRequests.user')->get();

            // Kết hợp hai tập hợp và loại bỏ nhóm trùng lặp
            $groups = $joinedGroups->merge($createdGroups)->unique('id');

            // Lấy danh sách thư mục và bài viết đã lưu của người dùng hiện tại
            $folders = Folder::where('user_id', $user->id)->get();
            $savedPosts = SavedPost::where('user_id', $user->id)->pluck('post_id')->toArray();
        }

        // Trả về view với các biến cần thiết
        return view('users.index', compact(
            'unreadNotifications',
            'users',
            'post',  // Chỉ truyền 'post' nếu có id tìm thấy
            'posts', // Truyền tất cả bài viết nếu không có id
            'folders',
            'savedPosts',
            'readNotifications',
            'groups',
            'group',
            'usersToFollow',
            'postFromNotification'
        ));
    }

    // Thêm method mới để xử lý sorting
    private function applySorting($query, $sort)
    {
        switch ($sort) {
            case 'hot':
                return $query->withCount('likes')
                    ->orderBy('likes_count', 'desc')
                    ->orderByRaw('COALESCE(published_at, created_at) DESC');

            case 'new':
                return $query->orderByRaw('COALESCE(published_at, created_at) DESC');

            default:
                return $query->orderByRaw('COALESCE(published_at, created_at) DESC');
        }
    }
    // Trang xử lý Profile người dùng
    public function show(User $user, $section = 'profile')
    {
        // Lấy ID từ đối tượng user đã được truyền vào
        $id = $user->id;

        $groups = Group::all();

        // Đếm số lượng bài viết đã xuất bản
        $publishedCount = Post::where('user_id', $id)->where('status', 'published')->count();

        // Lấy danh sách bài viết dạng draft
        $draftPosts = Post::where('user_id', $id)->where('status', 'draft')->get();
        $draftCount = $draftPosts->count();

        // Khởi tạo biến friends từ quan hệ đã định nghĩa trong model User
        $friends = $user->friends; // Lấy danh sách bạn bè của người dùng

        // Lấy danh sách bài viết yêu thích
        $favoritePosts = Post::with('user')
            ->whereHas('likes', function ($query) use ($id) {
                $query->where('user_id', $id);
            })
            ->get();

        // Lấy thông tin về tình trạng kết bạn
        $friendship = Auth::user()->sentFriendRequests->where('receiver_id', $user->id)->first();

        // Kiểm tra xem người dùng hiện tại có phải là người đang được xem hay không
        $isOwnProfile = Auth::user()->id === $user->id;

        $receivedFriendRequests = Auth::user()->receivedFriendRequests; // Lấy danh sách yêu cầu kết bạn nhận được

        // Lấy các thư mục của người dùng
        $folders = Folder::where('user_id', $id)->get();

        // Lấy tất cả nhóm mà người dùng đã tạo
        $ownedGroups = Group::where('creator_id', $id)->get();

        if ($section === 'friends') {
            // Truyền biến cần thiết
            return view('users.profile.friends', compact('friends', 'user', 'isOwnProfile', 'receivedFriendRequests', 'groups'));
        }

        // Trả về view và truyền dữ liệu người dùng cùng các bài viết của họ
        return view('users.profile.index', compact('user', 'publishedCount', 'draftCount', 'favoritePosts', 'friendship', 'isOwnProfile', 'ownedGroups', 'receivedFriendRequests', 'friends', 'groups', 'folders'));
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);

        // Xác thực dữ liệu nhập vào
        $request->validate([
            'username' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096', // Xác thực ảnh nền
        ]);

        // Cập nhật thông tin người dùng
        $user->username = $request->input('username');

        // Xử lý ảnh đại diện
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            if ($user->profile_picture) {
                Storage::delete('public/' . $user->profile_picture);
            }

            // Lưu ảnh mới
            $path = $request->file('avatar')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        // Xử lý ảnh nền
        if ($request->hasFile('cover_image')) {
            // Xóa ảnh nền cũ nếu có
            if ($user->cover_image) {
                Storage::delete('public/' . $user->cover_image);
            }

            // Lưu ảnh nền mới
            $coverPath = $request->file('cover_image')->store('images/covers', 'public');
            $user->cover_image = $coverPath;
        }

        // Lưu các thay đổi vào cơ sở dữ liệu
        $user->save();

        return redirect()->route('users.profile.index', $user->id)->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Kiểm tra quyền truy cập
        if (Auth::id() !== $user->id) {
            abort(403, 'Hành động không được phép.');
        }

        $user->delete();
        return redirect()->route('users.profile.index')->with('success', 'Xóa người dùng thành công');
    }

    public function edit(User $user)
    {
        return view('users.profile.edit', compact('user'));
    }

    public function friend(User $user)
    {
        // Lấy ID từ đối tượng user đã được truyền vào
        $id = $user->id;
        $friends = $user->friends; // Lấy danh sách bạn bè của người dùng
        // Kiểm tra xem người dùng hiện tại có phải là người đang được xem hay không
        $isOwnProfile = Auth::user()->id === $user->id;
        return view('users.profile.friends', compact('friends', 'user', 'isOwnProfile'));
    }
}
