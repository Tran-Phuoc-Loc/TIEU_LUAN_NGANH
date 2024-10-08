<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Group;
use App\Models\Post;
use App\Models\Category;

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

        // Chia sẻ biến $post của đến view published
        View::composer('users.posts.published', function ($view) {
            $posts = Post::where('status', 'published')->get();
            $view->with('posts', $posts);
        });

        // Chia sẻ biến categories tới tất cả các view
        View::composer('*', function ($view) {
            $categories = Category::all(); // Lấy tất cả các danh mục
            $view->with('categories', $categories); // Chia sẻ biến $categories
        });
    }
}
