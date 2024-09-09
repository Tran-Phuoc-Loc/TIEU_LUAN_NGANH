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
    Route::get('/dashboard', function () {
        $posts = Post::all();
        return view('users.index', compact('posts'));
    })->name('dashboard');

    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.profile');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

    // Route để quản lý bài viết
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/drafts', [PostController::class, 'drafts'])->name('posts.drafts');
    Route::post('/posts/{id}/publish', [PostController::class, 'publish'])->name('posts.publish'); // Route xuất bản bài viết
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');     // Route để xóa bài viết
});
