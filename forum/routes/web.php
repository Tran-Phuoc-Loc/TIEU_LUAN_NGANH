<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Models\Post;

// Route cho đăng nhập
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Route cho user
Route::resource('users', UserController::class);


// Roter cho categories 
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
// Route để hiển thị form tạo bài viết
Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/users', [UserController::class, 'index'])->name('users.index');


Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');



// Route để lưu bài viết mới
Route::post('posts', [PostController::class, 'store'])->name('posts.store');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $posts = Post::all(); // Lấy tất cả các bài viết
    return view('users.index', compact('posts')); // Truyền biến $posts vào view
})->middleware(['auth', 'verified'])->name('dashboard');


// Route cho phép người dùng xác thực rồi cho phép thực hiện các thao tác
Route::middleware('auth')->group(function () {
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.profile');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

});

require __DIR__.'/auth.php';
