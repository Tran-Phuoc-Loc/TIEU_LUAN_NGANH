<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductMessage;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    // Hiển thị danh sách tin nhắn
    public function index(Request $request)
    {
        // Lấy từ khóa tìm kiếm từ request (nếu có)
        $search = $request->input('search');
    
        // Lấy tất cả tin nhắn từ bảng product_messages kèm thông tin người gửi, người nhận và sản phẩm
        $messages = ProductMessage::with(['sender', 'receiver', 'product'])
            ->when($search, function ($query, $search) {
                // Lọc tin nhắn dựa trên từ khóa tìm kiếm
                $query->whereHas('sender', function ($q) use ($search) {
                    $q->where('username', 'like', '%' . $search . '%');
                })
                ->orWhereHas('receiver', function ($q) use ($search) {
                    $q->where('username', 'like', '%' . $search . '%');
                })
                ->orWhereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('content', 'like', '%' . $search . '%');
            })
            ->latest() // Sắp xếp tin nhắn mới nhất lên trên
            ->paginate(15); // Phân trang mỗi trang 15 tin nhắn
    
        return view('admin.messages.index', compact('messages', 'search'));
    }    

    // Chi tiết tin nhắn
    public function show(ProductMessage $productMessage)
    {
        $productMessage->load(['sender', 'receiver', 'product']);
        return view('admin.messages.show', compact('productMessage'));
    }
}
