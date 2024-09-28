<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    public function index()
    {
        $users = User::all();
        // Lấy tất cả bài viết đã xuất bản
        $posts = Post::with('user')
            ->where('status', 'published')
            ->get();
        return view('users.index', compact('users', 'posts')); // Truyền cả users và posts đến view
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
        $draftCount = 0;
        if (Auth::id() !== $user->id) {
            // Đếm số lượng bài viết ở dạng draft
            $draftCount = Post::where('user_id', $id)->where('status', 'draft')->count();
        }

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
