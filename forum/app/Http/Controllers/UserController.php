<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Models\SavedPost;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Folder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all(); // Lấy tất cả người dùng

        // Lấy tất cả bài viết đã xuất bản
        $posts = Post::with('user')->where('status', 'published')->get();

        // Kiểm tra xem người dùng đã đăng nhập chưa
        $group = Auth::check() ? Auth::user()->group : null; // Nếu đã đăng nhập thì lấy nhóm, nếu không thì null

        // Lấy danh sách thư mục của người dùng hiện tại
        $folders = Folder::where('user_id', Auth::id())->get();

        // Lấy danh sách bài viết đã lưu của người dùng hiện tại
        $savedPosts = SavedPost::where('user_id', Auth::id())->pluck('post_id')->toArray();

        // Trả về view với các biến cần thiết
        return view('users.index', compact('users', 'posts', 'group', 'folders', 'savedPosts'));
    }

    public function show(User $user)
    {
        // Dừng chương trình và hiển thị dữ liệu của user (dùng để kiểm tra)
        // dd($user);

        // Lấy ID từ đối tượng user đã được truyền vào
        $id = $user->id;

        // Đếm số lượng bài viết đã xuất bản
        $publishedCount = Post::where('user_id', $id)->where('status', 'published')->count();

        // Nếu người dùng hiện tại là chủ sở hữu của hồ sơ, đếm bài viết dạng draft
        // $draftCount = 0;
        // if (Auth::id() !== $user->id) {
        //     // Đếm số lượng bài viết ở dạng draft
        //     $draftCount = Post::where('user_id', $id)->where('status', 'draft')->count();
        // }
        // Kiểm tra các bài viết dạng draft
        $draftPosts = Post::where('user_id', $id)->where('status', 'draft')->get();
        $draftCount = $draftPosts->count();
        // Log::info('Draft Count:', ['draftCount' => $draftCount]);
        // Log::info('User ID:', ['userId' => $user->id]);
        // Trả về view và truyền dữ liệu người dùng cùng các bài viết của họ
        return view('users.profile.index', compact('user', 'publishedCount', 'draftCount'));
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);

        // Cập nhật thông tin người dùng
        $user->username = $request->input('username');

        // Xử lý ảnh
        if ($request->hasFile('avatar')) { // Đảm bảo tên trường khớp
            // Xóa ảnh cũ nếu có
            if ($user->profile_picture) {
                Storage::delete($user->profile_picture);
            }

            // Lưu ảnh mới
            $path = $request->file('avatar')->store('profile_pictures', 'public'); // Sử dụng 'avatar'
            $user->profile_picture = $path; // Cập nhật đường dẫn ảnh mới
        }

        $user->save(); // Lưu người dùng

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
}
