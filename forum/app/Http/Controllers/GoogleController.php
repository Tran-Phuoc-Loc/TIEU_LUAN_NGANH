<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // Chuyển hướng người dùng đến Google để đăng nhập
    public function redirectToGoogle()
    {
        session()->flush(); // Xóa tất cả session
        return Socialite::driver('google')->redirect();
    }

    // Xử lý callback từ Google
    public function handleGoogleCallback()
    {
        try {
            // Lấy thông tin người dùng từ Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Kiểm tra xem người dùng đã đăng ký với Google chưa
            $user = User::where('google_id', $googleUser->id)->first();

            // Nếu người dùng không tồn tại trong hệ thống, tạo mới
            if (!$user) {
                $user = User::create([
                    'username' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(Str::random(16)), // Mật khẩu ngẫu nhiên
                    'profile_picture' => $googleUser->avatar, // Lưu URL ảnh đại diện từ Google
                ]);
            }

            // Đăng nhập người dùng
            Auth::login($user);

            // Ghi thông tin người dùng vào log
            Log::info('Google User:', (array) $googleUser);
            Log::info('Created User:', (array) $user);

            // Chuyển hướng đến trang người dùng sau khi đăng nhập thành công
            return redirect()->route('users.index')->with('success', 'Đăng nhập thành công!');
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return redirect()->route('login')->with('error', 'Đăng nhập thất bại, vui lòng thử lại.');
        }
    }
}
