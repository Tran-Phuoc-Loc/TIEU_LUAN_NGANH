<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Group;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.users', function ($view) {
            $group = Group::first(); // Hoặc lấy nhóm theo cách mà bạn cần
            $view->with('group', $group);
        });

        // Chia sẻ nhóm của người dùng với tất cả các view
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $userGroups = $user->groups()->with('chats.user')->get(); // Lấy các nhóm cùng với tin nhắn của họ
                $view->with('userGroups', $userGroups);
            }
        });
    }
}
