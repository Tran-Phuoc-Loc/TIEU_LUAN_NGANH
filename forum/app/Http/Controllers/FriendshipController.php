<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FriendshipController extends Controller
{
    // Phương thức gửi yêu cầu kết bạn
    public function sendRequest($receiverId)
    {
        $currentUserId = Auth::id();

        // Kiểm tra yêu cầu tồn tại bằng cách sử dụng rõ ràng các cột `sender_id` và `receiver_id`
        $exists = Friendship::where(function ($query) use ($currentUserId, $receiverId) {
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
            'exists' => $exists,
        ]);

        if (!$exists) {
            Friendship::create([
                'sender_id' => $currentUserId,
                'receiver_id' => $receiverId,
                'status' => 'pending'
            ]);

            return back()->with('success', 'Yêu cầu kết bạn đã được gửi.');
        }

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

    // Phương thức từ chối yêu cầu kết bạn
    public function declineRequest($senderId)
    {
        $friendship = Friendship::where('sender_id', $senderId)
            ->where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($friendship) {
            $friendship->delete();

            return back()->with('success', 'Yêu cầu kết bạn đã bị từ chối.');
        }

        return back()->with('error', 'Yêu cầu kết bạn không tồn tại.');
    }

    // Phương thức hủy yêu cầu kết bạn
    public function cancelRequest($receiverId)
    {
        $friendship = Friendship::where('sender_id', Auth::id())
            ->where('receiver_id', $receiverId)
            ->where('status', 'pending')
            ->first();

        if ($friendship) {
            $friendship->delete();

            return back()->with('success', 'Yêu cầu kết bạn đã bị hủy.');
        }

        return back()->with('error', 'Không thể hủy yêu cầu này.');
    }
}
