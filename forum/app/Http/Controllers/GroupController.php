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

        // Lấy nhóm mà người dùng đã tham gia
        $joinedGroups = $user->groups()->with('creator', 'memberRequests.user')->get();

        // Lấy nhóm mà người dùng đã tạo
        $createdGroups = Group::where('creator_id', $user->id)->with('creator', 'memberRequests.user')->get();

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
        // Lấy thông tin nhóm và các thành viên trong nhóm cùng người tạo
        $group = Group::with('creator', 'users', 'posts')->findOrFail($id);

        // Lấy các bài viết liên quan đến nhóm (nếu có)
        $posts = Post::where('group_id', $id)->get();
        $members = $group->members;

        // Log thông tin nhóm để kiểm tra
        // Log::info('Group info: ', ['group' => $group]);

        // Trả về view chi tiết nhóm
        return view('users.groups.show', compact('group', 'posts', 'members'));
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

    public function leaveGroup(Group $group)
    {
        // Kiểm tra xem người dùng có phải là thành viên không
        if (!$group->members()->where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('info', 'Bạn không phải là thành viên của nhóm này.');
        }

        // Xóa người dùng khỏi nhóm
        $group->members()->detach(Auth::id());

        return redirect()->back()->with('success', 'Bạn đã rời khỏi nhóm.');
    }

    public function approveMember(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);

        // Kiểm tra xem người dùng có quyền chấp nhận không
        if (Auth::id() !== $group->creator_id) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }

        // Tìm yêu cầu tham gia từ người dùng
        $memberRequest = GroupJoinRequest::where('group_id', $groupId)
            ->where('user_id', $request->input('user_id'))
            ->first();

        if ($memberRequest) {
            // Chấp nhận yêu cầu
            $group->members()->attach($memberRequest->user_id);
            $memberRequest->delete(); // Xoá yêu cầu
            return redirect()->route('users.groups.show', $groupId)->with('success', 'Bạn đã chấp nhận yêu cầu tham gia nhóm.');
        }

        return redirect()->back()->with('error', 'Không tìm thấy yêu cầu tham gia.');
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
