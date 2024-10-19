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
        $totalReports = Report::count();

        // Những người dùng hoạt động nhiều nhất (bỏ admin ra)
        $mostActiveUsers = User::withCount('posts', 'comments')
            ->where('role', '!=', 'admin')
            ->orderByDesc('posts_count')
            ->orderByDesc('comments_count')
            ->take(5) // Lấy 5 người dùng hoạt động nhiều nhất
            ->get();

        // Số nhóm tạo ra
        $totalGroups = Group::count();

        // Thống kê danh mục bài viết (danh mục có nhiều bài viết nhất)
        $topCategories = Category::withCount('posts')
            ->orderByDesc('posts_count')
            ->take(5) // Lấy 5 danh mục có nhiều bài viết nhất
            ->get();

        // Tỷ lệ bài viết theo trạng thái (xử lý trước khi gửi sang view)
        $postStatusData = Post::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Tạo hai mảng: một cho labels và một cho counts
        $statusLabels = $postStatusData->pluck('status')->toArray();
        $statusCounts = $postStatusData->pluck('count')->toArray();

        // Dữ liệu cho biểu đồ hoạt động
        $activityData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $postsCount = Post::whereDate('created_at', $date)->count();
            $commentsCount = Comment::whereDate('created_at', $date)->count();

            $activityData[] = [
                'date' => $date,
                'posts_count' => $postsCount,
                'comments_count' => $commentsCount,
            ];
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalPosts',
            'totalLikes',
            'newRegistrationsToday',
            'totalReports',
            'mostActiveUsers',
            'totalGroups',
            'statusLabels',
            'statusCounts',
            'topCategories',
            'activityData'
        ));
    }
}
