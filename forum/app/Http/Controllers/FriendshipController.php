<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\FriendshipRequestNotification;

class FriendshipController extends Controller
{
    // Phương thức gửi yêu cầu kết bạn
    public function sendRequest($receiverId)
    {
        $currentUserId = Auth::id();

        // Kiểm tra yêu cầu kết bạn có tồn tại và trạng thái của nó
        $friendship = Friendship::where(function ($query) use ($currentUserId, $receiverId) {
            $query->where('sender_id', $currentUserId)
                ->where('receiver_id', $receiverId);
        })
            ->orWhere(function ($query) use ($currentUserId, $receiverId) {
                $query->where('sender_id', $receiverId)
                    ->where('receiver_id', $currentUserId);
            })
            ->first();

        Log::info('Kiểm tra tồn tại yêu cầu kết bạn:', [
            'current_user_id' => $currentUserId,
            'receiver_id' => $receiverId,
            'friendship' => $friendship,
        ]);

        // Nếu yêu cầu không tồn tại, bị từ chối hoặc đã hủy, cho phép tạo mới
        if (!$friendship || $friendship->status === 'declined') {
            // Nếu yêu cầu đã bị từ chối, cho phép gửi lại yêu cầu mới
            if ($friendship && $friendship->status === 'declined') {
                // Xóa yêu cầu đã từ chối
                $friendship->delete();
            }

            // Tạo yêu cầu kết bạn mới
            Friendship::create([
                'sender_id' => $currentUserId,
                'receiver_id' => $receiverId,
                'status' => 'pending', // Đánh dấu yêu cầu đang chờ xử lý
            ]);

            // Tìm người nhận và người gửi
            $receiver = User::find($receiverId); // Tìm người nhận
            $sender = User::find($currentUserId); // Tìm người gửi

            // Gửi thông báo yêu cầu kết bạn
            $receiver->notify(new FriendshipRequestNotification($sender));

            return back()->with('success', 'Yêu cầu kết bạn đã được gửi.');
        }

        // Nếu đã có yêu cầu kết bạn trước đó, trả về lỗi
        return back()->with('error', 'Bạn đã gửi yêu cầu trước đó hoặc đã có yêu cầu từ người dùng này.');
    }

    // Phương thức chấp nhận yêu cầu kết bạn
    public function acceptRequest($senderId)
    {
        $currentUserId = Auth::id();

        // Kiểm tra yêu cầu kết bạn
        $friendship = Friendship::where([
            ['sender_id', $senderId],
            ['receiver_id', $currentUserId],
            ['status', 'pending']
        ])->first();

        if ($friendship) {
            $friendship->update(['status' => 'accepted']);

            // Lấy lại danh sách yêu cầu kết bạn sau khi cập nhật
            $receivedFriendRequests = Friendship::where('receiver_id', $currentUserId)
                ->where('status', 'pending')
                ->get();

            // Lấy danh sách bạn bè mới để hiển thị
            $friends = User::whereHas('friendships', function ($query) use ($currentUserId) {
                $query->where('status', 'accepted')
                    ->where(function ($query) use ($currentUserId) {
                        $query->where('sender_id', $currentUserId)
                            ->orWhere('receiver_id', $currentUserId);
                    });
            })->get();

            return back()->with([
                'success' => 'Yêu cầu kết bạn đã được chấp nhận.',
                'receivedFriendRequests' => $receivedFriendRequests,
                'friends' => $friends
            ]);
        }

        return back()->with('error', 'Yêu cầu kết bạn không tồn tại.');
    }

    // Phương thức từ chối yêu cầu kết bạn (người nhận từ chối yêu cầu)
    public function declineRequest($senderId)
    {
        $currentUserId = Auth::id();

        // Tìm yêu cầu kết bạn mà bạn nhận được
        $friendship = Friendship::where('sender_id', $senderId)
            ->where('receiver_id', $currentUserId)
            ->where('status', 'pending')
            ->first();

        if ($friendship) {
            // Cập nhật trạng thái thành "declined" thay vì xóa
            $friendship->update(['status' => 'declined']);
            return back()->with('success', 'Yêu cầu kết bạn đã bị từ chối.');
        }

        return back()->with('error', 'Yêu cầu kết bạn không tồn tại.');
    }

    // Phương thức hủy yêu cầu kết bạn (người gửi hủy yêu cầu)
    public function cancelRequest($userId)
    {
        $friendship = Friendship::where(function ($query) use ($userId) {
            $query->where('sender_id', Auth::id())->where('receiver_id', $userId);
        })
            ->orWhere(function ($query) use ($userId) {
                $query->where('sender_id', $userId)->where('receiver_id', Auth::id());
            })
            ->first();

        if ($friendship) {
            $friendship->delete(); // Xóa yêu cầu kết bạn
        }

        return back()->with('status', 'Yêu cầu kết bạn đã bị hủy.');
    }
}
