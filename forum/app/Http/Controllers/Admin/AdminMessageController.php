<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductMessage;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    // Hiển thị danh sách tin nhắn
    public function index()
    {
        // Lấy tất cả tin nhắn từ bảng product_messages, kèm thông tin người gửi, người nhận và sản phẩm
        $messages = ProductMessage::with(['sender', 'receiver', 'product'])
            ->latest() // Sắp xếp tin nhắn mới nhất lên trên
            ->paginate(15); // Phân trang mỗi trang 15 tin nhắn

        return view('admin.messages.index', compact('messages'));
    }

    // Chi tiết tin nhắn
    public function show(ProductMessage $productMessage)
    {
        $productMessage->load(['sender', 'receiver', 'product']);
        return view('admin.messages.show', compact('productMessage'));
    }
}
