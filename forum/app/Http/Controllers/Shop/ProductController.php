<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Product;
use App\Models\ProductImage;
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
    public function index(Request $request)
    {
        // Lấy giá trị sắp xếp từ request
        $sort = $request->input('sort_by');

        // Khởi tạo truy vấn
        $query = Product::with('user');

        // Áp dụng sắp xếp nếu có
        $query = $this->applySorting($query, $sort);

        // Kiểm tra nếu có tham số 'id' trong URL
        if ($request->has('id')) {
            // Lọc sản phẩm theo id nếu có tham số 'id'
            $products = $query->where('id', $request->id)->get(); // Sử dụng get() khi không cần phân trang
        } else {
            // Sử dụng paginate khi cần phân trang
            $products = $query->paginate(9);
        }

        // Lấy các thông tin cần thiết cho view
        $receiver = Auth::user();
        $folders = Folder::with('savedPosts')->where('user_id', Auth::id())->get();
        $groups = Group::all();
        $relatedProducts = Product::inRandomOrder()->limit(5)->get();

        // Trả về view với các biến cần thiết
        return view('products.index', compact('products', 'receiver', 'relatedProducts', 'groups', 'folders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // Hiển thị form tạo sản phẩm mới
    public function create()
    {
        $categories = ProductCategory::all();
        $groups = Group::all();
        return view('products.create', compact('categories', 'groups'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4048', // Ảnh đại diện
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4048' // Nhiều ảnh khác
        ]);

        // Lấy tất cả dữ liệu từ request
        $data = $request->all();

        // Gán user_id của người dùng đăng nhập
        $data['user_id'] = Auth::id();
        $data['status'] = 'in_stock'; // Mặc định là còn hàng

        // Lưu ảnh đại diện (nếu có)
        if ($request->hasFile('image')) {
            $imageName = \Ramsey\Uuid\Guid\Guid::uuid4()->toString() . '.' . $request->file('image')->extension();
            $imagePath = $request->file('image')->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath; // Lưu vào bảng products
        }

        // Tạo sản phẩm mới
        $product = Product::create($data);

        // Lưu các hình ảnh khác vào bảng `product_images` (nếu có)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = \Ramsey\Uuid\Guid\Guid::uuid4()->toString() . '.' . $image->extension();
                $path = $image->storeAs('products', $filename, 'public');

                // Tạo bản ghi cho từng ảnh
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }

        // Chuyển hướng về trang danh sách sản phẩm
        return redirect()->route('products.index')->with('success', 'Sản phẩm được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    // Hiển thị chi tiết một sản phẩm
    public function show($id)
    {
        // Tìm sản phẩm theo ID, bao gồm thông tin người bán và các ảnh liên quan
        $product = Product::with('user', 'images')->findOrFail($id);
        $groups = Group::all();
        return view('products.show', compact('product', 'groups'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    // Hiển thị form chỉnh sửa sản phẩm
    public function edit(Product $product)
    {
        $categories = ProductCategory::all();
        $groups = Group::all();
        return view('products.edit', compact('product', 'categories', 'groups'));
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

    // Thêm method mới để xử lý sorting
    private function applySorting($query, $sort)
    {
        switch ($sort) {
            case 'name_asc':
                return $query->orderBy('name', 'asc');
            case 'name_desc':
                return $query->orderBy('name', 'desc');
            case 'price_asc':
                return $query->orderBy('price', 'asc');
            case 'price_desc':
                return $query->orderBy('price', 'desc');
            case 'newest':
                return $query->orderBy('created_at', 'desc');
            default:
                return $query;
        }
    }
}
