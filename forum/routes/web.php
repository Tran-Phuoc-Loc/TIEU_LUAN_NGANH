<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminGroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\NotificationController;
use App\Models\Group;
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
    // Dashboard của admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/get-activity-data', [AdminController::class, 'getActivityData']);

    // Quản lý groups
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('groups', AdminGroupController::class);
        Route::get('admin/groups/{group}', [AdminGroupController::class, 'show'])->name('admin.groups.show');

    });

    // Route cho quản lý User
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::delete('admin/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Route cho quản lý danh mục
    Route::prefix('admin/categories')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('admin.categories.index'); // Danh sách danh mục
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('admin.categories.create'); // Hiển thị trang tạo danh mục
        Route::post('/', [AdminCategoryController::class, 'store'])->name('admin.categories.store'); // Lưu danh mục mới
        Route::get('{category}/edit', [AdminCategoryController::class, 'edit'])->name('admin.categories.edit'); // Hiển thị trang chỉnh sửa danh mục
        Route::put('{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update'); // Cập nhật danh mục
        Route::delete('{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy'); // Xóa danh mục
    });

    // Routes cho báo cáo
    Route::prefix('admin/reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('admin.reports.index'); // Danh sách báo cáo
        Route::get('{id}', [ReportController::class, 'show'])->name('admin.reports.show'); // Xem chi tiết báo cáo
        Route::post('{id}/process', [ReportController::class, 'process'])->name('admin.reports.process'); // Xử lý báo cáo
    });

    // Quản lý bài viết
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('posts', AdminPostController::class);
        Route::post('posts/bulk-action', [AdminPostController::class, 'bulkAction'])->name('posts.bulkAction');
    });
});

// Route cho người dùng
Route::middleware(['auth'])->group(function () {
    // Route cho trang dashboard
    Route::get('/dashboard', function () {
        $posts = Post::all(); // Lấy tất cả bài viết
        return view('users.index', compact('posts')); // Hiển thị trang người dùng với danh sách bài viết
    })->name('dashboard');

    // Route để tạo thư mục
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    // Route cho trang hiển thị danh sách thư mục
    Route::get('users/folders', [FolderController::class, 'index'])->name('users.folders.index');

    // Route API để lấy các bài viết đã lưu của người dùng
    // Route::delete('/users/posts/unsave-post', [FolderController::class, 'unsavePost']); // xóa bài viết
    Route::get('/api/saved-posts', [FolderController::class, 'getSavedPosts']);
    Route::get('api/posts/{postId}/comments', [FolderController::class, 'getComments']);
    Route::delete('/api/posts/{postId}/unsave', [FolderController::class, 'unsavePost']);
    Route::delete('/folders/{folder_id}', [FolderController::class, 'deleteFolder'])->name('folders.delete');
    Route::post('/folders/{folder_id}/rename', [FolderController::class, 'renameFolder'])->name('folders.rename');

    // Route cho hồ sơ người dùng
    Route::prefix('users')->group(function () {
        // Hiển thị hồ sơ người dùng
        Route::get('{user}', [UserController::class, 'show'])->name('users.profile.index'); // Hiển thị hồ sơ người dùng

        // Chỉnh sửa hồ sơ người dùng
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('users.profile.edit'); // Hiển thị trang chỉnh sửa hồ sơ người dùng
        Route::put('{user}', [UserController::class, 'update'])->name('users.profile.update'); // Xử lý cập nhật hồ sơ người dùng
    });

    // Route::get('/users/posts', [UserController::class, 'index'])->name('users.profile.posts'); // Hiển thị danh sách bài viết của người dùng

    // Route để quản lý bài viết
    Route::prefix('users/posts')->group(function () {
        // Route để hiển thị và tạo bài viết
        Route::get('/create', [PostController::class, 'create'])->name('users.posts.create'); // Hiển thị trang tạo bài viết
        Route::post('/', [PostController::class, 'store'])->name('users.posts.store'); // Xử lý lưu bài viết mới

        // Route để hiển thị danh sách bài viết
        Route::get('/drafts', [PostController::class, 'drafts'])->name('users.posts.drafts'); // Hiển thị danh sách bài viết ở trạng thái draft
        Route::get('/published', [PostController::class, 'published'])->name('users.posts.published'); // Hiển thị danh sách bài viết đã xuất bản

        // Route để quản lý bài viết đã lưu
        Route::get('/savepost', [PostController::class, 'showSavedPosts'])->name('users.posts.savePost');
        Route::post('/save-post', [PostController::class, 'savePost'])->name('posts.savePost');

        // Route để chỉnh sửa bài viết
        Route::get('{post}/edit', [PostController::class, 'edit'])->name('posts.edit'); // Hiển thị trang chỉnh sửa bài viết
        Route::put('{post}', [PostController::class, 'update'])->name('posts.update'); // Cập nhật bài viết
        Route::put('{post}/recall', [PostController::class, 'recall'])->name('posts.recall'); // Gọi lại bài viết từ trạng thái đã xuất bản về nháp

        // Route để xóa bài viết
        Route::delete('{post}', [PostController::class, 'destroy'])->name('posts.destroy'); // Xóa bài viết

        // Route để xuất bản bài viết
        Route::post('{post}/publish', [PostController::class, 'publish'])->name('posts.publish'); // Xuất bài viết ra khỏi dạng draft

        // Route cho bình luận
        Route::post('{post}/comments', [CommentController::class, 'store'])->name('comments.store'); // Tạo bình luận cho bài viết
        Route::get('{post}', [PostController::class, 'show'])->name('posts.show'); // Hiển thị bài viết cùng với bình luận
        Route::post('{post}/like', [PostController::class, 'like'])->name('posts.like'); // Lượt thích của bài viết

        // Để lấy danh sách bình luận của bài viết dưới dạng JSON
        Route::get('/{post}/comments', [CommentController::class, 'index']);

        // Route để xóa bình luận
        // Route::delete('{post}/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy')
        // ->where(['comment' => '[0-9]+']); // Đảm bảo comment là một số nguyên
    });

    // Route quản lý bình luận bài viết
    Route::prefix('comments')->group(function () {
        Route::post('{comment}/like', [CommentController::class, 'like']); // Lượt thích cho bình luận bài viết
    });
    Route::delete('users/posts/{post}/comments/{id}', [CommentController::class, 'destroy']);





    // Route quản lý Group
    Route::prefix('users')->group(function () {
        // Đặt route không có tham số động lên trước
        Route::get('/groups/user-groups', [GroupController::class, 'userGroups'])->name('users.groups.index'); // Danh sách các nhóm mà người dùng đã tham gia
        Route::get('/groups/create', [GroupController::class, 'create'])->name('users.groups.create'); // Tạo nhóm mới
        Route::post('/groups', [GroupController::class, 'store'])->name('users.groups.store'); // Xử lý việc lưu nhóm mới được tạo

        // Sau đó là các route có tham số động
        Route::get('/groups/{id}', [GroupController::class, 'show'])->name('users.groups.show'); // Hiển thị thông tin chi tiết của một nhóm cụ thể
        Route::get('/groups/{group}/chat', [ChatController::class, 'index'])->name('groups.chat'); // Truy cập vào trang chat của một nhóm
        Route::post('/groups/{group}/chat', [ChatController::class, 'store'])->name('chats.store'); // Xử lý việc gửi tin nhắn trong một nhóm
        Route::post('groups/{group}/join', [GroupController::class, 'joinGroup'])->name('groups.join');
        Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
        Route::post('groups/{group}/leave', [GroupController::class, 'leaveGroup'])->name('groups.leave');
        // Định nghĩa route cho việc chấp nhận yêu cầu tham gia nhóm
        Route::post('/groups/{group}/approve', [GroupController::class, 'approveMember'])->name('groups.approve');
        Route::delete('/groups/{group}/kick/{user}', [GroupController::class, 'kickMember'])->name('groups.kick');
    });

    // Route cho categories 
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index'); // Nếu có các hành động CRUD
    Route::get('/categories/{slug}/posts', [CategoryController::class, 'showPosts'])->name('categories.posts');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});

Route::get('/check-login', function () {
    return response()->json(['isLoggedIn' => Auth::check()]);
});
