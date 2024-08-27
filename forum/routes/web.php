<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;

// Route cho đăng nhập
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Route cho user
Route::resource('users', UserController::class);


// Roter cho categories 
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
// Route để hiển thị form tạo bài viết
Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
Route::resource('posts', PostController::class);
// Route để lưu bài viết mới
Route::post('posts', [PostController::class, 'store'])->name('posts.store');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('users.index'); // Trỏ đến users/index.blade.php
})->middleware(['auth', 'verified'])->name('dashboard');

// Route cho phép người dùng xác thực rồi cho phép thực hiện các thao tác
Route::middleware('auth')->group(function () {
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.profile');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

});

require __DIR__.'/auth.php';
