<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Group;
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

    public function showPosts($slug)
    {
        // Lấy danh mục theo slug
        $category = Category::where('slug', $slug)->firstOrFail();
        $groups = Group::all();
        // Lấy tất cả bài viết thuộc danh mục này
        $posts = $category->posts()->where('status', 'published')->get();

        // Trả về view cùng với danh sách bài viết
        return view('users.categories.posts', compact('category', 'posts', 'groups'));
    }
}
