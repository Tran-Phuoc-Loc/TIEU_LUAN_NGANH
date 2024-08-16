<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Kiểm tra xem người dùng có đang hoạt động không
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản của bạn không hoạt động.']);
            }

            return redirect()->route('home');
        }

        return back()->withErrors(['email' => 'Thông tin xác thực không hợp lệ']);
    }

    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed',
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'status' => 'active', // Trạng thái mặc định đang hoạt động
        ]);

        return redirect()->route('login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }

    // Phương pháp vô hiệu hóa tài khoản người dùng
    public function deactivateUser($userId)
    {
        $user = User::find($userId);
        
        if ($user) {
            $user->status = 'inactive';
            $user->save();
            
            return redirect()->back()->with('thành công', 'Người dùng đã vô hiệu hóa thành công.');
        }
        
        return redirect()->back()->withErrors(['User không tìm thấy.']);
    }
}
