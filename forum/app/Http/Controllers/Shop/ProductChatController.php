<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ProductMessage;
use App\Events\NewProductMessage;
use App\Models\Product;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductChatController extends Controller
{
    public function show($productId, $receiverId)
    {
        $product = Product::findOrFail($productId);
        $receiver = User::findOrFail($receiverId);

        // Lấy tất cả nhóm
        $groups = Group::all();

        // Lấy danh sách tin nhắn giữa người bán (Auth::id()) và người mua (receiverId)
        $messages = ProductMessage::where('product_id', $productId)
            ->where(function ($query) use ($receiverId) {
                $query->where(function ($q) use ($receiverId) {
                    // Lọc các tin nhắn giữa người bán và người mua
                    $q->where('sender_id', Auth::id())
                        ->where('receiver_id', $receiverId);
                })
                    ->orWhere(function ($q) use ($receiverId) {
                        // Lọc các tin nhắn giữa người mua và người bán
                        $q->where('sender_id', $receiverId)
                            ->where('receiver_id', Auth::id());
                    });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.product', compact('product', 'receiver', 'messages', 'groups'));
    }

    public function send(Request $request, $productId, $receiverId)
    {
        // Kiểm tra nếu tin nhắn trống thì không gửi
        if (empty($request->message)) {
            return response()->json(['status' => 'error', 'message' => 'Tin nhắn không được để trống.']);
        }

        // Tạo và lưu tin nhắn mới vào cơ sở dữ liệu
        $message = ProductMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'product_id' => $productId,
            'content' => $request->message,
        ]);

        // Gửi sự kiện thông báo cho người nhận
        event(new NewProductMessage($message));

        // Trả về dữ liệu JSON với thông tin tin nhắn mới
        return response()->json([
            'status' => 'success',
            'message' => $message->content,
            'sender_username' => Auth::user()->username,
            'timestamp' => $message->created_at->diffForHumans(),
            'product_id' => $productId,
            'receiver_id' => $receiverId
        ]);
    }

    // Phương thức hiển thị danh sách người mua đã nhắn tin cho người bán
    public function sellerChatList()
    {
        $userId = Auth::id();  // Lấy ID của người bán (hoặc người đang đăng nhập)

        // Lấy tất cả nhóm 
        $groups = Group::all();

        // Lọc tin nhắn giữa người bán và người mua (chỉ lấy người mua)
        $customers = ProductMessage::where(function ($query) use ($userId) {
            // Tin nhắn từ người mua đến người bán hoặc ngược lại
            $query->where('sender_id', '!=', $userId)  // Không lấy người bán
                ->where('receiver_id', $userId);  // Người bán là người nhận
        })
            ->distinct('sender_id')  // Chỉ lấy mỗi người mua 1 lần
            ->with('sender', 'product')  // Tải thông tin người gửi và sản phẩm
            ->paginate(5);

        return view('chat.seller', compact('customers', 'groups'));
    }
}
