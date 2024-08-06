<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

// Route::get('/', [PostController::class, 'app']);
// Trang chính
Route::get('/', function () {
    return view('home');
})->name('home');

// Trang liên hệ
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::resource('posts', PostController::class);
// Xử lý gửi thông tin liên hệ
// Route::post('contact', [ContactController::class, 'send'])->name('contact.send');
