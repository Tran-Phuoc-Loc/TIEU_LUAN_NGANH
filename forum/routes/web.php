<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\RoleMiddleware;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

// Route cho đăng nhập
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Route cho đăng xuất
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Route cho đăng ký
Route::get('register', [RegisterController::class, 'create'])->name('register');
Route::post('register', [RegisterController::class, 'store']);

// Route cho user
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Route cho trang chủ
Route::get('/', function () {
    return view('welcome');
});
// Route cho tìm kiếm
Route::get('/users/posts', [PostController::class, 'index'])->name('users.posts.index');
Route::post('/admin/reports/store', [ReportController::class, 'store'])->name('admin.reports.store');
// Route cho admin
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
     // Route cho danh sách danh mục
    Route::get('admin/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
    // Các route khác cho quản lý danh mục
    Route::get('admin/categories/create', [AdminCategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('admin/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('admin/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('admin/categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('admin/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // Routes cho báo cáo
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/{id}', [ReportController::class, 'show'])->name('admin.reports.show');
    Route::post('/admin/reports/{id}/process', [ReportController::class, 'process'])->name('admin.reports.process');

});

// Route cho người dùng
Route::middleware(['auth'])->group(function () {
    // Route cho trang dashboard
    Route::get('/dashboard', function () {
        $posts = Post::all(); // Lấy tất cả bài viết
        return view('users.index', compact('posts')); // Hiển thị trang người dùng với danh sách bài viết
    })->name('dashboard');



    // Route cho hồ sơ người dùng

    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.profile.index'); // Hiển thị hồ sơ người dùng
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.profile.edit'); // Hiển thị trang chỉnh sửa hồ sơ người dùng
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.profile.update');
    // Route::get('/users/posts', [UserController::class, 'index'])->name('users.profile.posts'); // Hiển thị danh sách bài viết của người dùng

    // Route để quản lý bài viết
    Route::prefix('posts')->group(function () {
        Route::get('/create', [PostController::class, 'create'])->name('posts.create'); // Hiển thị trang tạo bài viết
        Route::post('/', [PostController::class, 'store'])->name('posts.store'); // Xử lý lưu bài viết mới
        Route::get('/drafts', [PostController::class, 'drafts'])->name('posts.drafts'); // Hiển thị danh sách bài viết ở trạng thái draft
        Route::put('{post}/recall', [PostController::class, 'recall'])->name('posts.recall'); // Gọi lại bài viết từ trạng thái đã xuất bản về nháp
        Route::get('{post}/edit', [PostController::class, 'edit'])->name('posts.edit'); // Hiển thị trang chỉnh sửa bài viết
        Route::delete('{id}', [PostController::class, 'destroy'])->name('posts.destroy'); // Xóa bài viết
        Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');// Cập nhật bài viết
        Route::post('/posts/{id}/publish', [PostController::class, 'publish'])->name('posts.publish'); // Xuất bài viết ra khỏi dạng draft
        Route::get('/published', [PostController::class, 'published'])->name('posts.published'); // Hiển thị danh sách bài viết đã xuất bản
        Route::post('{post}/comments', [CommentController::class, 'store'])->name('comments.store'); // Để tạo bình luận cho bài viết
        Route::get('{postId}', [CommentController::class, 'show']); // Để hiển thị bài viết cùng với bình luận
        Route::post('{postId}/like', [PostController::class, 'like']); // Lượt thích của bài viết 
    });

    // Route quản lý bình luận bài viết
    Route::prefix('comments')->group(function () {
        Route::post('{commentId}/like', [CommentController::class, 'like']); // Lượt thích cho bình luận bài viết
    });

    // Route cho categories 
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index'); // Nếu có các hành động CRUD
});
// Để lấy danh sách bình luận của bài viết dưới dạng JSON
Route::get('/posts/{post}/comments', [CommentController::class, 'index']);

Route::get('/check-login', function () {
    return response()->json(['isLoggedIn' => Auth::check()]);
});
