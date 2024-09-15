<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Post;

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

    public function show($id)
    {
        // Tìm người dùng theo ID và lấy bài viết của họ
        $user = User::findOrFail($id);
        $publishedCount = Post::where('user_id', $id)->where('status', 'published')->count(); // Đếm số lượng bài viết đã xuất bản
        $draftCount = Post::where('user_id', $id)->where('status', 'draft')->count(); // Đếm số lượng bài viết ở dạng draft

        // Kiểm tra nếu người dùng đang đăng nhập cố gắng truy cập hồ sơ của chính mình
        if (Auth::id() !== $user->id) {
            abort(403, 'Hành động không được phép.');
        }

        // Trả về view và truyền dữ liệu người dùng cùng các bài viết của họ
        return view('users.profile', compact('user', 'publishedCount', 'draftCount'));
    }

    public function edit(User $user)
    {
        // Kiểm tra nếu người dùng hiện tại có quyền chỉnh sửa
        if (Auth::id() !== $user->id) {
            abort(403, 'Hành động không được phép.');
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        // Kiểm tra quyền truy cập
        if (Auth::id() !== $user->id) {
            abort(403, 'Hành động không được phép.');
        }
    
        $user->update($request->all());
        return redirect()->route('users.index')->with('success', 'Người dùng cập nhật thành công');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
    
        // Kiểm tra quyền truy cập
        if (Auth::id() !== $user->id) {
            abort(403, 'Hành động không được phép.');
        }
    
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Xóa người dùng thành công');
    }
}
