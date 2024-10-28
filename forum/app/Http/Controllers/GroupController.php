<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupJoinRequest;
use App\Models\Post;

class GroupController extends Controller
{
    public function userGroups()
    {
        $user = Auth::user();

        // Nạp trước các nhóm mà người dùng đã tham gia
        $joinedGroups = $user->groups()->with(['creator', 'memberRequests.user'])->get();

        // Nạp trước các nhóm mà người dùng đã tạo
        $createdGroups = Group::where('creator_id', $user->id)
            ->with(['creator', 'memberRequests.user'])
            ->get();

        // Kết hợp hai tập hợp và loại bỏ nhóm trùng lặp
        $groups = $joinedGroups->merge($createdGroups)->unique('id');

        return view('users.groups.index', compact('groups'));
    }

    public function create()
    {
        return view('users.groups.create');
    }

    public function store(Request $request)
    {
        // Kiểm tra dữ liệu nhận được
        // dd($request->all());
        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requires_approval' => 'boolean',
        ]);

        // Nếu checkbox không được chọn, giá trị sẽ là null
        $requiresApproval = $request->has('requires_approval');

        // dd($requiresApproval); // Kiểm tra giá trị của requiresApproval

        // Kiểm tra giá trị trước khi lưu
        // dd([
        //     'name' => $request->input('name'),
        //     'description' => $request->input('description'),
        //     'creator_id' => Auth::id(),
        //     'requires_approval' => $requiresApproval,
        // ]);
        // Tạo nhóm mới
        $group = Group::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'creator_id' => Auth::id(),
            'requires_approval' => $requiresApproval,
        ]);

        // Thêm người tạo nhóm vào nhóm luôn
        $group->users()->attach(Auth::id());

        return redirect()->route('users.groups.show', $group->id)->with('success', 'Nhóm đã được tạo thành công!');
    }

    public function show($id)
    {
        // Lấy thông tin nhóm, các thành viên trong nhóm, các bài viết liên quan đến nhóm và tin nhắn
        $group = Group::with(['creator', 'users', 'posts', 'chats.user'])->findOrFail($id);

        // Lấy các bài viết liên quan đến nhóm (nếu có) - có thể bỏ qua vì đã lấy trong Group
        // $posts = Post::where('group_id', $id)->get(); // Không cần nữa vì đã có trong $group

        // Lấy các thành viên trong nhóm
        $members = $group->members;

        // Lấy các yêu cầu tham gia nhóm còn chờ xử lý
        $joinRequests = $group->joinRequests()->where('status', 'pending')->get();

        // Log thông tin nhóm để kiểm tra
        // Log::info('Group info: ', ['group' => $group]);

        // Trả về view chi tiết nhóm
        return view('users.groups.show', compact('group', 'joinRequests', 'members'));
    }

    public function destroy(Group $group)
    {
        if (Auth::id() !== $group->creator_id) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa nhóm này.');
        }

        $group->delete();
        return redirect()->route('users.groups.index')->with('success', 'Nhóm đã được xóa thành công.');
    }

    public function joinGroup(Group $group)
    {
        // Kiểm tra xem người dùng đã là thành viên hoặc đã gửi yêu cầu chưa
        if (
            $group->members()->where('user_id', Auth::id())->exists() ||
            $group->memberRequests()->where('user_id', Auth::id())->exists()
        ) {
            return redirect()->back()->with('error', 'Bạn đã yêu cầu tham gia hoặc đã là thành viên của nhóm này.');
        }

        // Nếu nhóm yêu cầu phê duyệt
        if ($group->requires_approval) {
            // Tạo yêu cầu tham gia
            GroupJoinRequest::create([
                'group_id' => $group->id,
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);

            return redirect()->back()->with('success', 'Yêu cầu tham gia đã được gửi cho chủ nhóm.');
        } else {
            // Nếu không yêu cầu phê duyệt, thêm người dùng vào nhóm ngay lập tức
            $group->members()->attach(Auth::id());
            return redirect()->back()->with('success', 'Bạn đã tham gia nhóm.');
        }
    }

    public function approveRequest(Group $group, $userId)
    {
        // Xử lý duyệt yêu cầu tham gia nhóm
        $group->members()->attach($userId);
        $group->joinRequests()->where('user_id', $userId)->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Đã duyệt yêu cầu tham gia.');
    }

    public function rejectRequest(Group $group, $userId)
    {
        // Xử lý từ chối yêu cầu tham gia nhóm
        $group->joinRequests()->where('user_id', $userId)->delete();

        return redirect()->back()->with('success', 'Đã từ chối yêu cầu tham gia.');
    }


    public function kickMember($groupId, $userId)
    {
        $group = Group::findOrFail($groupId);

        // Kiểm tra quyền truy cập
        if (Auth::id() !== $group->creator_id) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }

        // Xóa thành viên khỏi nhóm
        $group->members()->detach($userId);

        // Xóa yêu cầu tham gia (nếu có)
        $group->memberRequests()->where('user_id', $userId)->delete();

        return redirect()->back()->with('success', 'Người dùng đã bị đuổi khỏi nhóm.');
    }
}
