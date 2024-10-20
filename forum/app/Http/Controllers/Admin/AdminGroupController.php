<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;

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
            ->paginate(10); // Số nhóm hiển thị mỗi trang

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
        $group->delete();
        return redirect()->route('admin.groups.index')->with('success', 'Xóa Group thành công .');
    }

    public function show($id)
    {
        $group = Group::with(['members', 'creator'])->findOrFail($id);
        return view('admin.groups.show', compact('group'));
    }
}
