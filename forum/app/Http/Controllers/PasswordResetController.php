<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    public function showResetRequestForm()
    {
        return view('auth.passwords.email');
    }
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Tìm người dùng theo email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy người dùng']);
        }

        // Tạo token và gửi email
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Gửi email chứa liên kết đặt lại mật khẩu
        Mail::send('auth.passwords.reset', ['token' => $token], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Đặt lại mật khẩu');
        });

        return back()->with('status', 'Đã gửi liên kết đặt lại mật khẩu vào email của bạn.');
    }
    public function reset(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|confirmed|min:6',
        'token' => 'required',
    ]);

    // Kiểm tra token
    $passwordReset = DB::table('password_resets')->where('token', $request->token)->where('email', $request->email)->first();
    if (!$passwordReset) {
        return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ.']);
    }

    // Cập nhật mật khẩu
    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();

    // Xóa token sau khi sử dụng
    DB::table('password_resets')->where('email', $request->email)->delete();

    return redirect()->route('login')->with('status', 'Mật khẩu đã được đặt lại thành công. Bạn có thể đăng nhập.');
}
}
