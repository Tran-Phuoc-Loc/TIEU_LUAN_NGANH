<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\SavedPost;
use App\Models\Folder;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        // Lấy tất cả nhóm 
        $groups = Group::all();
        $categories = Category::all();
        return view('users.categories.index', compact('categories', 'groups'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index');
    }

    public function showPosts($slug, Request $request)
    {
        $user = Auth::user();
    
        // Lấy danh mục theo slug
        $category = Category::where('slug', $slug)->firstOrFail();
    
        // Tạo truy vấn để lấy tất cả bài viết thuộc danh mục này mà không có `group_id`
        $query = $category->posts()
            ->where('status', 'published')
            ->whereNull('group_id');
    
        // Áp dụng sắp xếp cho bài viết không thuộc nhóm
        $query = $this->applySorting($query, $request->sort);
        $posts = $query->get();
    
        $userGroups = collect(); // Sử dụng collection rỗng nếu người dùng chưa đăng nhập
        $groupPosts = collect();
    
        // Nếu người dùng đã đăng nhập, lấy thêm bài viết từ các nhóm mà họ là thành viên
        if ($user) {
            // Lấy tất cả các nhóm mà người dùng hiện tại là thành viên
            $userGroups = $user->groups()->pluck('groups.id');
    
            // Tạo truy vấn để lấy bài viết từ nhóm mà người dùng là thành viên, thuộc danh mục hiện tại
            $groupQuery = $category->posts()
                ->where('status', 'published')
                ->whereIn('group_id', $userGroups);
    
            // Áp dụng sắp xếp cho bài viết thuộc nhóm
            $groupQuery = $this->applySorting($groupQuery, $request->sort);
            $groupPosts = $groupQuery->get();
        }
    
        // Kết hợp bài viết không có `group_id` và bài viết từ các nhóm mà người dùng là thành viên
        $posts = $posts->merge($groupPosts);
    
        // Lấy danh sách thư mục và bài viết đã lưu của người dùng nếu có
        $folders = [];
        $savedPosts = [];
        if ($user) {
            $folders = Folder::where('user_id', $user->id)->get();
            $savedPosts = SavedPost::where('user_id', $user->id)->pluck('post_id')->toArray();
        }
    
        return view('users.categories.posts', compact('category', 'posts', 'folders', 'savedPosts'));
    }
    
    // Thêm method mới để xử lý sorting
    private function applySorting($query, $sort)
    {
        switch ($sort) {
            case 'hot':
                return $query->withCount('likes')
                            ->orderBy('likes_count', 'desc')
                            ->orderByRaw('COALESCE(published_at, created_at) DESC');
            
            case 'new':
                return $query->orderByRaw('COALESCE(published_at, created_at) DESC');
            
            default:
                return $query->orderByRaw('COALESCE(published_at, created_at) DESC');
        }
    }
    
}
