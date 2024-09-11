<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\RoleMiddleware;
use App\Models\Post;

// Route cho đăng nhập
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Route cho đăng xuất
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Route cho đăng ký
Route::get('register', [RegisterController::class, 'create'])->name('register');
Route::post('register', [RegisterController::class, 'store']);

// Route cho user
Route::resource('users', UserController::class);

// Route cho categories 
Route::resource('categories', CategoryController::class); // Nếu có các hành động CRUD

// Route cho trang chủ
Route::get('/', function () {
    return view('welcome');
});

// Route cho admin
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});

// Route cho người dùng
Route::middleware(['auth'])->group(function () {
    // Route cho trang dashboard
    Route::get('/dashboard', function () {
        $posts = Post::all(); // Lấy tất cả bài viết
        return view('users.index', compact('posts')); // Hiển thị trang người dùng với danh sách bài viết
    })->name('dashboard');

    // Route cho hồ sơ người dùng
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.profile'); // Hiển thị hồ sơ người dùng
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit'); // Hiển thị trang chỉnh sửa hồ sơ người dùng
    Route::get('/users/posts', [UserController::class, 'index'])->name('users.posts'); // Hiển thị danh sách bài viết của người dùng

    // Route để quản lý bài viết
    Route::prefix('posts')->group(function () {
        Route::get('/create', [PostController::class, 'create'])->name('posts.create'); // Hiển thị trang tạo bài viết
        Route::post('/', [PostController::class, 'store'])->name('posts.store'); // Xử lý lưu bài viết mới
        Route::get('/drafts', [PostController::class, 'drafts'])->name('posts.drafts'); // Hiển thị danh sách bài viết ở trạng thái draft
        Route::post('{id}/publish', [PostController::class, 'publish'])->name('posts.publish'); // Xuất bản bài viết
        Route::get('{post}/edit', [PostController::class, 'edit'])->name('posts.edit'); // Hiển thị trang chỉnh sửa bài viết
        Route::put('{post}', [PostController::class, 'update'])->name('posts.update'); // Cập nhật bài viết
        Route::delete('{id}', [PostController::class, 'destroy'])->name('posts.destroy'); // Xóa bài viết
        Route::get('/published', [PostController::class, 'published'])->name('posts.published'); // Hiển thị danh sách bài viết đã xuất bản
    });
});