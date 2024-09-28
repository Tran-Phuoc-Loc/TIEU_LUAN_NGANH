<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Tạo người dùng mới
        try {
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Xác thực người dùng
            Auth::guard()->login($user);

            // Chuyển hướng đến trang chính
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Đăng ký không thành công: ' . $e->getMessage()]);
        }
    }
}
