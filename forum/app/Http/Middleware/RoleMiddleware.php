<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Xử lý một yêu cầu đến.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // kiểm tra lỗi
        // dd('RoleMiddleware is being executed');
        if (!Auth::check()) {
            // Lỗi được ghi vào logs -> laravel.log
            // Log::info('Người dùng chưa được xác thực');
            return redirect('login');
        }

        $user = Auth::user();
        // Lỗi được ghi vào logs -> laravel.log
        // Log::info('User role:', ['user_id' => $user->id, 'role' => $user->role]);

        if ($user->role !== $role) {
            // Lỗi được ghi vào logs -> laravel.log
            // Log::warning('Người dùng không có vai trò cần thiết', ['user_id' => $user->id, 'required_role' => $role]);
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Lỗi được ghi vào logs -> laravel.log
        // Log::info('Người dùng có vai trò cần thiết', ['user_id' => $user->id, 'role' => $role]);
        return $next($request);
    }
}
