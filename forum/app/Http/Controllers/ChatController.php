<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Chat;
use App\Models\User;
use App\Models\PrivateMessage;

class ChatController extends Controller
{
    public function store(Request $request, $groupId)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        Chat::create([
            'group_id' => $groupId,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()->back();
    }

    public function index($groupId, $receiverId = null)
    {
        // Lấy thông tin nhóm cùng với các tin nhắn và thông tin người dùng gửi tin
        $group = Group::with(['chats.user'])->findOrFail($groupId);

        // Lấy danh sách bạn bè (hoặc tất cả người dùng trừ chính người dùng đang đăng nhập)
        $user = Auth::user();
        // Lấy danh sách bạn bè đã kết bạn
        $friends = Auth::user()->friends;

        $receiver = $receiverId ? User::findOrFail($receiverId) : null; // Kiểm tra nếu có receiverId
        $messages = []; // Khởi tạo mảng tin nhắn

        // Nếu có receiver, lấy các tin nhắn giữa người dùng và receiver
        if ($receiver) {
            $messages = PrivateMessage::where(function ($query) use ($user, $receiver) {
                $query->where('sender_id', $user->id)->where('receiver_id', $receiver->id);
            })->orWhere(function ($query) use ($user, $receiver) {
                $query->where('sender_id', $receiver->id)->where('receiver_id', $user->id);
            })->with('sender')->get();
        }

        // Trả về view chi tiết nhóm và truyền dữ liệu nhóm đã lấy và danh sách bạn bè
        return view('users.groups.chat', [
            'userGroups' => $user->groups,
            'friends' => $friends,
            'receiver' => $receiver,
            'messages' => $messages,
            'group' => $group,
        ]);
    }

    public function showPrivateChat($receiverId, $groupId = null)
    {
        $receiver = User::find($receiverId); // Lấy thông tin người nhận
        // Lấy danh sách bạn bè đã kết bạn
        $friends = Auth::user()->friends;

        // Lấy danh sách tin nhắn
        $messages = PrivateMessage::where(function ($query) use ($receiverId) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', Auth::id());
        })->orderBy('created_at')
            ->get();

        return view('users.groups.chat', compact('messages', 'friends', 'receiver', 'groupId'));
    }

    public function storePrivateMessage(Request $request, $receiverId)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $message = PrivateMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'content' => $request->message,
        ]);

        // Thay vì chuyển hướng, trả về JSON
        return response()->json([
            'sender' => [
                'username' => Auth::user()->username,
                'id' => Auth::id(),
            ],
            'content' => $message->content,
            'created_at' => $message->created_at,
        ]);
    }
}
