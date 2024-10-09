<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class FolderController extends Controller
{
    public function create(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Tạo thư mục mới
        $folder = Folder::create([
            'user_id' => Auth::id(), // Lưu ID của người dùng hiện tại
            'name' => $request->name,
        ]);

        // Trả về phản hồi JSON
        return response()->json([
            'success' => true,
            'folder_id' => $folder->id, // Trả về ID của thư mục mới
            'message' => 'Thư mục đã được tạo thành công!',
        ]);
    }
}
