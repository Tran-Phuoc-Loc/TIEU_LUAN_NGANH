<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('users.categories.index', compact('categories'));
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
        // Debug category
        // dd('Category:', $category->toArray());
        // Lấy tất cả bài viết thuộc danh mục này
        $posts = $category->posts()->where('status', 'published')->get();
        // Log::info('Category: ', $category->toArray());
        // Log::info('Posts: ', $posts->toArray());
        //     // Debug posts
        // dd('Posts:', $posts->toArray());
        // dd($category);
        // dd([
        //     'Category' => $category->toArray(),
        //     'Posts' => $posts->map(function ($post) {
        //         return [
        //             'id' => $post->id,
        //             'title' => $post->title,
        //             'slug' => $post->slug,
        //             'status' => $post->status,
        //             'category_id' => $post->category_id,
        //             'created_at' => $post->created_at,
        //         ];
        //     })->toArray()
        // ]);
        // Trả về view cùng với danh sách bài viết
        return view('users.categories.posts', compact('category', 'posts'));
    }
}
