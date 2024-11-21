<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupJoinRequest;
use App\Models\Category;
use App\Models\SavedPost;
use App\Models\Folder;

class GroupController extends Controller
{
    public function userGroups()
    {
        $user = Auth::user();

        // Lấy danh sách các nhóm mà người dùng đã tham gia
        $joinedGroups = $user->groups()->with(['creator', 'memberRequests.user', 'users'])->get();

        // Lấy các nhóm mà người dùng đã tạo
        $createdGroups = Group::where('creator_id', $user->id)
            ->with(['creator', 'memberRequests.user', 'users'])
            ->get();

        // Kết hợp hai tập hợp và loại bỏ trùng lặp dựa trên `id`
        $groups = $joinedGroups->merge($createdGroups)->unique('id');

        // Lấy danh sách các nhóm gợi ý mà người dùng chưa tham gia
        $userGroupIds = $groups->pluck('id'); // Lấy danh sách các ID của nhóm đã tham gia
        $suggestedGroups = Group::whereNotIn('id', $userGroupIds)
            ->with('creator')
            ->inRandomOrder()
            ->take(5)
            ->get();

        return view('users.groups.index', compact('groups', 'suggestedGroups'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('users.groups.create', compact('groups'));
    }

    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'visibility' => 'required|in:public,private',
            'requires_approval' => 'nullable|boolean', // cho phép null nếu checkbox không chọn
        ]);

        // Nếu checkbox không được chọn, giá trị sẽ là null
        $requiresApproval = $request->has('requires_approval');

        // Tạo nhóm mới
        $group = Group::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'visibility' => $request->input('visibility'),
            'creator_id' => Auth::id(),
            'requires_approval' => $requiresApproval,
        ]);

        // Xử lý ảnh nếu có
        if ($request->hasFile('avatar')) {
            // Lưu ảnh mới và lấy đường dẫn lưu trữ
            $avatarPath = $request->file('avatar')->store('groups/avatars', 'public');
            $group->avatar = $avatarPath; // Cập nhật đường dẫn avatar cho nhóm
            $group->save(); // Lưu lại nhóm với avatar
        }

        // Thêm người tạo nhóm vào nhóm luôn
        $group->users()->attach(Auth::id());

        return redirect()->route('users.groups.show', $group->id)->with('success', 'Nhóm đã được tạo thành công!');
    }

    public function show($id)
    {
        // Lấy thông tin nhóm cùng với các bài viết, thành viên, tin nhắn
        $group = Group::with(['creator', 'users', 'posts.user', 'chats.user'])->findOrFail($id);
        $user = Auth::user();

        // Lấy danh sách các danh mục cho bài viết
        $categories = Category::all();

        // Lấy các thành viên trong nhóm
        $members = $group->users;

        // Lấy các yêu cầu tham gia nhóm còn chờ xử lý
        $joinRequests = $group->joinRequests()->where('status', 'pending')->get();

        // Lấy danh sách thư mục và bài viết đã lưu của người dùng nếu có
        $folders = [];
        $savedPosts = [];
        if ($user) {

            // Lấy danh sách thư mục và bài viết đã lưu của người dùng hiện tại
            $folders = Folder::where('user_id', $user->id)->get();
            $savedPosts = SavedPost::where('user_id', $user->id)->pluck('post_id')->toArray();
        }
        // Lấy các bài viết của nhóm
        $posts = $group->posts;

        // Trả về view và truyền biến $user
        return view('users.index', compact('group', 'joinRequests', 'members', 'categories', 'savedPosts', 'user', 'posts'));
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

    public function leaveGroup($id)
    {
        $user = Auth::user();
        $group = Group::findOrFail($id);

        // Kiểm tra nếu người dùng là thành viên của nhóm
        if ($group->isMember($user)) {
            // Xóa người dùng khỏi nhóm
            $group->users()->detach($user->id);

            return redirect()->route('users.groups.index')->with('success', 'Bạn đã rời khỏi nhóm thành công.');
        }

        return redirect()->route('users.groups.index')->with('error', 'Bạn không phải là thành viên của nhóm này.');
    }

    public function edit(Group $group)
    {
        // Kiểm tra xem người dùng có phải là chủ nhóm không
        if ($group->creator_id !== Auth::id()) {
            return redirect()->route('groups.show', $group->id)->with('error', 'Bạn không có quyền chỉnh sửa nhóm này.');
        }

        return view('users.groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        // Kiểm tra xem người dùng có phải là chủ nhóm không
        if ($group->creator_id !== Auth::id()) {
            return redirect()->route('groups.show', $group->id)->with('error', 'Bạn không có quyền chỉnh sửa nhóm này.');
        }

        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'visibility' => 'required|in:public,private',
        ]);

        // Cập nhật thông tin nhóm
        $group->name = $request->input('name');
        $group->description = $request->input('description');
        $group->visibility = $request->input('visibility');

        // Xử lý ảnh nếu có
        if ($request->hasFile('avatar')) {
            // Lưu ảnh mới
            $avatarPath = $request->file('avatar')->store('groups/avatars', 'public');
            $group->avatar = $avatarPath;
        }

        $group->save();

        return redirect()->route('users.groups.index', $group->id)->with('success', 'Nhóm đã được cập nhật thành công!');
    }
}
