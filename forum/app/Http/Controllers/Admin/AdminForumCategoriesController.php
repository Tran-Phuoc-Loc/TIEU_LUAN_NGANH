<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminForumCategoriesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // dd($user->role); // Kiểm tra giá trị
        $categories = ForumCategory::paginate(10);
        return view('admin.forum.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.forum.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ForumCategory::create($request->only(['name', 'description']));

        return redirect()->route('admin.forum.categories.index')->with('success', 'Danh mục mới đã được tạo.');
    }

    public function edit($id)
    {
        $category = ForumCategory::findOrFail($id);

        return view('admin.forum.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = ForumCategory::findOrFail($id);
        $category->update($request->only(['name', 'description']));

        return redirect()->route('admin.forum.categories.index')->with('success', 'Danh mục đã được cập nhật.');
    }

    public function destroy($id)
    {
        $category = ForumCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.forum.categories.index')->with('success', 'Danh mục đã được xóa.');
    }
}
