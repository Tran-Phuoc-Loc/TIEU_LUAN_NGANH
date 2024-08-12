<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
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

        $posts = $user->posts ?: collect(); // Khởi tạo với một tập hợp rỗng nếu không có bài viết

        // Trả về view và truyền dữ liệu người dùng cùng các bài viết của họ
        return view('users.profile', compact('user', 'posts'));
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
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
