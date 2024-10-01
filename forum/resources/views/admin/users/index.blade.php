@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<h2>Quản lý người dùng</h2>

<!-- Thanh tìm kiếm -->
<form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm người dùng..." value="{{ $search }}">
        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
    </div>
</form>

<!-- Bảng quản lý người dùng -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên người dùng</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if ($users->isEmpty())
        <tr>
            <td colspan="7" class="text-center">Không tìm thấy người dùng</td>
        </tr>
        @else
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->username }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role }}</td>
            <td>{{ $user->status }}</td>
            <td>{{ $user->created_at }}</td>
            <td>
                <a href="#" class="btn btn-sm btn-warning">Chỉnh sửa</a>

                <!-- Ẩn nút xóa nếu là admin -->
                @if ($user->role !== 'admin')
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                </form>
                @endif

            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
@endsection