<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
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
 
         // Tổng số lượt yêu thích (giả sử bạn có một trường 'likes')
         $totalLikes = Post::sum('likes_count');
 
         // Số người đăng ký hôm nay
         $newRegistrationsToday = User::whereDate('created_at', Carbon::today())->count();
 
         // Hoạt động gần đây (lấy từ bảng Post và Comment với người dùng liên quan)
         $recentActivities = [
             'posts' => Post::with('user')->latest()->take(5)->get(),
             'comments' => Comment::with('user')->latest()->take(5)->get()
         ];
 
         return view('admin.dashboard', compact('totalUsers', 'totalPosts', 'totalLikes', 'newRegistrationsToday', 'recentActivities'));
     }
}