<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        // Lấy từ khóa tìm kiếm từ request
        $search = $request->input('search');

        // Truy vấn người dùng với điều kiện tìm kiếm (nếu có)
        $query = User::query();

        if ($search) {
            $query->where('username', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        }

        // Đảm bảo admin user đứng đầu danh sách
        $users = $query->orderByRaw("FIELD(role, 'admin') DESC")->get();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function destroy($id)
    {
        $user = User::find($id);

        // Kiểm tra nếu người dùng có vai trò 'admin', không cho phép xóa
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Không thể xóa người dùng có vai trò admin.');
        }

        // Xóa người dùng nếu không phải admin
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công.');
    }
}
