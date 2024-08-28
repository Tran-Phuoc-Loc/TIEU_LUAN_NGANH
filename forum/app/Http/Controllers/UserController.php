<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Policies\UserPolicy;

class UserController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    public function index()
    {
        $users = User::all();
        $posts = Post::all(); // Truy xuất tất cả các bài viết từ database
        return view('users.index', compact('users', 'posts')); // Truyền cả users và posts đến view
    }

    public function show($id)
    {
        // Tìm người dùng theo ID và lấy bài viết của họ
        $user = User::findOrFail($id);
        $posts = $user->posts ?: collect(); // Khởi tạo với một tập hợp rỗng nếu không có bài viết

        // Kiểm tra nếu người dùng đang đăng nhập cố gắng truy cập hồ sơ của chính mình
        if (Auth::id() !== $user->id) {
            abort(403, 'Hành động không được phép.');
        }

        // Trả về view và truyền dữ liệu người dùng cùng các bài viết của họ
        return view('users.profile', compact('user', 'posts'));
    }

    public function edit(User $user)
    {
        // Sử dụng policy để kiểm tra quyền
        $this->authorize('edit', $user);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
