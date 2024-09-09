<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\RoleMiddleware;
use App\Models\Post;
use App\Http\Controllers\Auth\RegisterController;

// Route cho đăng nhập
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Route cho đăng xuất
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
// Route cho đăng ký
Route::get('register', [RegisterController::class, 'create'])->name('register');
Route::post('register', [RegisterController::class, 'store']);

// Route cho user
Route::resource('users', UserController::class);

// Route cho categories 
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

// Route để hiển thị form tạo bài viết
Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Route để lưu bài viết mới
Route::post('posts', [PostController::class, 'store'])->name('posts.store');

// Route cho trang chủ
Route::get('/', function () {
    return view('welcome');
});

// Route cho admin
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});

// Route cho người dùng
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $posts = Post::all(); // Lấy tất cả các bài viết
        return view('users.index', compact('posts')); // Truyền biến $posts vào view
    })->name('dashboard');

    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.profile');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
});
