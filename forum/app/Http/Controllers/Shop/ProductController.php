<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Guid\Guid;

class ProductController extends Controller
{
    public function __construct()
    {
        // Middleware auth chỉ áp dụng cho các hành động create, store, edit, update, destroy
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    // Hiển thị danh sách sản phẩm
    public function index()
    {
        // Sử dụng paginate với quan hệ 'category'
        $products = Product::with('user')->latest()->paginate(9);
        $receiver = Auth::user();
        $groups = Group::all();

        // Lấy các sản phẩm khác để hiển thị trong sidebar
        $relatedProducts = Product::inRandomOrder()->limit(5)->get();

        return view('products.index', compact('products', 'receiver', 'relatedProducts', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // Hiển thị form tạo sản phẩm mới
    public function create()
    {
        $categories = ProductCategory::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // Lưu sản phẩm mới vào cơ sở dữ liệu
    public function store(Request $request)
    {
        // Validate dữ liệu nhập vào
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Kiểm tra ảnh
        ]);

        // Lấy tất cả dữ liệu từ request
        $data = $request->all();

        // Chỉ cho phép người dùng đã đăng nhập tạo sản phẩm
        if (Auth::check()) {
            // Gán user_id của người dùng đang đăng nhập
            $data['user_id'] = Auth::id(); // Lấy id của người dùng đã đăng nhập
        } else {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để tạo sản phẩm.');
        }

        // Gán trạng thái mặc định là 'in_stock' khi tạo sản phẩm mới
        $data['status'] = 'in_stock';  // Mặc định là còn hàng

        // Kiểm tra và lưu ảnh nếu có
        if ($request->hasFile('image')) {
            // Tạo tên ảnh duy nhất bằng UUID
            $imageName = \Ramsey\Uuid\Guid\Guid::uuid4()->toString() . '.' . $request->file('image')->extension();

            // Lưu ảnh vào thư mục 'products' trong storage public
            $imagePath = $request->file('image')->storeAs('products', $imageName, 'public');

            // Lưu đường dẫn ảnh vào dữ liệu sản phẩm
            $data['image'] = $imagePath;
        }

        // Tạo sản phẩm mới
        Product::create($data);

        // Chuyển hướng về trang danh sách sản phẩm với thông báo thành công
        return redirect()->route('products.index')->with('success', 'Sản phẩm được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    // Hiển thị chi tiết một sản phẩm
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    // Hiển thị form chỉnh sửa sản phẩm
    public function edit(Product $product)
    {
        $categories = ProductCategory::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    // Cập nhật sản phẩm trong cơ sở dữ liệu
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'status' => 'required|in:in_stock,out_of_stock',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        // Cập nhật thông tin sản phẩm
        $data = $request->only('name', 'description', 'price', 'product_category_id');

        // Xử lý cập nhật hình ảnh nếu có
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    // Xóa một sản phẩm
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Kiểm tra và xóa ảnh liên quan nếu có
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Xóa sản phẩm
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được xóa thành công.');
    }
}
