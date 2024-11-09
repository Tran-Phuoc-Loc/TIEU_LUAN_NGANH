<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminGroupController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Nếu có tìm kiếm, lọc theo tên hoặc mô tả nhóm
        $groups = Group::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->with('creator') // Tải chủ nhóm
            ->paginate(10); // Phân trang

        return view('admin.groups.index', compact('groups'));
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $group->update($request->all());
        return redirect()->route('admin.groups.index')->with('success', 'Cập nhật Group thành công.');
    }

    public function destroy(Group $group)
    {
        // Lấy tất cả các thành viên của nhóm
        $members = $group->members;
    
        // Tạo nội dung thông báo
        $notificationData = [
            'title' => 'Nhóm đã bị xóa',
            'message' => "Nhóm '{$group->name}' đã bị admin xóa.",
        ];
    
        // Gửi thông báo cho từng thành viên
        foreach ($members as $member) {
            DB::table('notifications')->insert([
                'id' => Str::uuid(), // Tạo UUID cho thông báo
                'type' => 'group_deleted',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $member->id,
                'data' => json_encode($notificationData),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        // Xóa nhóm
        $group->delete();
    
        return redirect()->route('admin.groups.index')->with('success', 'Xóa nhóm thành công.');
    }

    public function show($id)
    {
        $group = Group::with(['members', 'creator'])->withCount('chats') // Đếm số lượng tin nhắn
        ->findOrFail($id);
        return view('admin.groups.show', compact('group'));
    }
}
