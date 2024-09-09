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
        
        // Kiểm tra người dùng đã đăng nhập hay chưa
        if (!Auth::check()) {
            // Log::info('Người dùng chưa được xác thực');
            return redirect('login');
        }

        $user = Auth::user();

        // Kiểm tra xem người dùng có vai trò cần thiết hay không
        if ($user->role !== $role) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Chặn truy cập vào các route admin nếu người dùng không phải là admin
        if ($user->role !== 'admin' && $request->is('admin/*')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
