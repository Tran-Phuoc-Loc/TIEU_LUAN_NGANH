<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Group;
use App\Models\Report;
use App\Models\Category;
use App\Models\Comment;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Tổng số người dùng
        $totalUsers = User::count();

        // Tổng số bài viết
        $totalPosts = Post::count();

        // Tổng số lượt yêu thích
        $totalLikes = Post::sum('likes_count');

        // Số người đăng ký hôm nay
        $newRegistrationsToday = User::whereDate('created_at', Carbon::today())->count();

        // Tổng số báo cáo vi phạm
        $totalReports = Report::count(); // Giả sử bạn có bảng `reports`

        // Hoạt động gần đây (lấy từ bảng Post và Comment với người dùng liên quan)
        $recentActivities = [
            'posts' => Post::with('user')->latest()->take(5)->get(),
            'comments' => Comment::with('user')->latest()->take(5)->get()
        ];

        // 1. Những người dùng hoạt động nhiều nhất (bỏ admin ra)
        $mostActiveUsers = User::withCount('posts', 'comments')
            ->where('role', '!=', 'admin') 
            ->orderByDesc('posts_count')
            ->orderByDesc('comments_count')
            ->take(5) // Lấy 5 người dùng hoạt động nhiều nhất
            ->get();


        // 2. Số nhóm tạo ra
        $totalGroups = Group::count();

        // 3. Tỷ lệ bài viết theo trạng thái
        $postStatusCount = Post::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // 4. Thống kê danh mục bài viết (danh mục có nhiều bài viết nhất)
        $topCategories = Category::withCount('posts')
            ->orderByDesc('posts_count')
            ->take(5) // Lấy 5 danh mục có nhiều bài viết nhất
            ->get();

        return view('admin.dashboard', compact('totalUsers', 'totalPosts', 'totalLikes', 'newRegistrationsToday', 'totalReports', 'recentActivities', 'mostActiveUsers', 'totalGroups', 'postStatusCount', 'topCategories'));
    }
}
