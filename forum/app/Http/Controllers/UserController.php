<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Policies\UserPolicy;

class UserController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        // Tìm người dùng theo ID
        $user = User::findOrFail($id);

        // Lấy các bài viết của người dùng
        $posts = $user->posts; //  Quan hệ posts trong model User
        $user = User::find($id);

        // Kiểm tra tính tồn tại của user
        if (!$user) {
            abort(404); //  Kích hoạy lỗi 404
        }
        // Kiểm tra nếu người dùng đang đăng nhập cố gắng truy cập hồ sơ của chính mình
        if (Auth::id() !== $user->id) {
            // Tùy chọn, bạn có thể hiển thị lỗi 403
            abort(403, 'Hành động không được phép.');
        }

        $posts = $user->posts ?: collect(); // Khởi tạo với một tập hợp rỗng nếu không có bài viết


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
