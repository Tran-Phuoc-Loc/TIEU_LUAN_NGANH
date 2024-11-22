<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Models\User;

class AdminProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Kiểm tra xem có từ khóa tìm kiếm không
        $search = $request->get('search');

        // Nếu có từ khóa tìm kiếm, áp dụng tìm kiếm
        $products = Product::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%'); // Tìm kiếm theo tên sản phẩm
        })->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Hiển thị form tạo sản phẩm mới.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        //
    }

    /**
     * Lưu sản phẩm mới vào cơ sở dữ liệu.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'status' => 'required|in:pending,approved,rejected,sold',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Gán trạng thái mặc định là 'pending' nếu không có trạng thái nào được gửi từ người dùng
        $data['status'] = 'pending';

        // Kiểm tra và lưu ảnh nếu có
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/products', 'public');
            $data['image'] = $imagePath; // Lưu đường dẫn ảnh vào cơ sở dữ liệu
        }

        // Lưu sản phẩm mới
        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        // Lấy danh sách các danh mục sản phẩm
        $categories = ProductCategory::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Cập nhật sản phẩm đã chỉnh sửa.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'status' => 'required|in:pending,approved,rejected,sold',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $data = $request->all();

        $data['price'] = str_replace(',', '', $data['price']); // Xử lý giá (price) với định dạng chuỗi
        

        // Kiểm tra và lưu ảnh nếu có
        if ($request->hasFile('image')) {
            // Tạo tên ảnh duy nhất bằng UUID
            $imageName = \Ramsey\Uuid\Guid\Guid::uuid4()->toString() . '.' . $request->file('image')->extension();

            // Lưu ảnh vào thư mục 'products' trong storage public
            $imagePath = $request->file('image')->storeAs('products', $imageName, 'public');

            // Lưu đường dẫn ảnh vào dữ liệu sản phẩm
            $data['image'] = $imagePath;
        }

        // Cập nhật thông tin sản phẩm
        $product->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = \Str::uuid() . '.' . $image->extension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
    
                $product->additionalImages()->create([
                    'image' => $imagePath,
                ]);
            }
        }

        // Gửi thông báo đến người dùng
        $user = $product->user; // Bảng Product có liên kết với bảng User thông qua user_id
        if ($user) {
            $user->notify(new \App\Notifications\ProductUpdated($product));
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Xóa sản phẩm.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        $productName = $product->name;

        // Gửi thông báo đến người dùng trước khi xóa
        $user = $product->user; // Bảng Product có liên kết với bảng User thông qua user_id
        if ($user) {
            $user->notify(new \App\Notifications\ProductDeleted($productName));
        }

        // Xóa sản phẩm
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
