<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Post;

class GroupController extends Controller
{
    public function userGroups()
    {
        $user = Auth::user();   

        // Kết hợp nhóm đã tham gia và nhóm đã tạo
        $groups = $user->groups->merge(Group::where('creator_id', $user->id)->get());

        return view('users.groups.index', compact('groups'));
    }

    public function create()
    {
        return view('users.groups.create');
    }

    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Tạo nhóm mới
        $group = Group::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'creator_id' => Auth::id(),
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

        // Log thông tin nhóm để kiểm tra
        // Log::info('Group info: ', ['group' => $group]);

        // Trả về view chi tiết nhóm
        return view('users.groups.show', compact('group', 'posts'));
    }
}
