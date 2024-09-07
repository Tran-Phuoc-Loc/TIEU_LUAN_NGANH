<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        dd('Middleware RedirectIfAuthenticated called');
        if (Auth::guard($guard)->check()) {
            return redirect('/home'); // Hoặc trang khác sau khi đã đăng nhập
        }

        return $next($request);
    }
}
