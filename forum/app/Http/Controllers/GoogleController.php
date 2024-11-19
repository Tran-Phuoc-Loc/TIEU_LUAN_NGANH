<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
     // Chuyển hướng người dùng đến Google để đăng nhập
     public function redirectToGoogle()
     {
         return Socialite::driver('google')->redirect();
     }
 
     // Xử lý callback từ Google
     public function handleGoogleCallback()
     {
         $googleUser = Socialite::driver('google')->stateless::user();
 
         // Tìm user trong hệ thống
         $user = User::where('email', $googleUser->email)->first();
 
         if (!$user) {
             // Tạo user mới nếu chưa tồn tại
             $user = User::create([
                 'name' => $googleUser->name,
                 'email' => $googleUser->email,
                 'google_id' => $googleUser->id,
                 'password' => bcrypt('default_password'), // Đặt mật khẩu mặc định
             ]);
         }
 
         // Đăng nhập user
         Auth::login($user);
 
         return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
     }
}
