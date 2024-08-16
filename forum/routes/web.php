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


// Roter cho categories vs posts
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

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
