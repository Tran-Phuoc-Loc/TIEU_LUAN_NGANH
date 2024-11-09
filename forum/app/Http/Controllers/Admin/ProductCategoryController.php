<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Hiển thị danh sách các danh mục sản phẩm
    public function index(Request $request)
    {
        // Lấy từ khóa tìm kiếm từ request
        $search = $request->get('search');

        // Nếu có từ khóa tìm kiếm, lọc theo tên danh mục hoặc mô tả
        $categories = ProductCategory::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        })->paginate(10);
        
        // Trả về view với danh sách danh mục đã lọc và phân trang
        return view('admin.product_categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // Hiển thị form tạo danh mục mới
    public function create()
    {
        return view('admin.product_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // Lưu danh mục mới vào cơ sở dữ liệu
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ProductCategory::create($request->all());

        return redirect()->route('admin.product_categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    // Hiển thị chi tiết một danh mục
    public function show(ProductCategory $category)
    {
        return view('admin.product_categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    // Hiển thị form chỉnh sửa danh mục
    public function edit(ProductCategory $product_category)
    {
        // dd($category);
        return view('admin.product_categories.edit', compact('product_category'));
    }

    /**
     * Update the specified resource in storage.
     */
    // Cập nhật danh mục trong cơ sở dữ liệu
    public function update(Request $request, ProductCategory $product_category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $product_category->update($request->all());

        return redirect()->route('admin.product_categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    // Xóa một danh mục
    public function destroy(ProductCategory $product_category)
    {
        $product_category->delete();

        return redirect()->route('admin.product_categories.index')->with('success', 'Category deleted successfully.');
    }
}
