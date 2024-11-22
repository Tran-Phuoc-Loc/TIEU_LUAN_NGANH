@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Quản lý Group</h1>

    <!-- Form tìm kiếm -->
    <form action="{{ route('admin.groups.index') }}" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm nhóm..." value="{{ request()->query('search') }}" aria-label="Tìm kiếm nhóm" aria-describedby="search-button">
            <button class="btn btn-primary" type="submit" id="search-button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($groups->isEmpty())
    <p>Không có nhóm nào.</p>
    @else
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Avatar</th> <!-- Cột avatar -->
                <th>Tên Group</th>
                <th>Mô tả</th>
                <th>Chủ nhóm</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groups as $group)
            <tr>
                <td>{{ $group->id }}</td>
                <td>
                    <!-- Hiển thị avatar -->
                    <img src="{{ $group->avatar ? asset('storage/' . $group->avatar) : asset('groups/avatars/group_icon.png') }}"
                        alt="Avatar"
                        class="rounded thumbnail"
                        style="width: 50px; height: 50px; object-fit: cover;">
                </td>
                <td>{{ $group->name }}</td>
                <td>{{ Str::limit($group->description, 50) }}</td>
                <td>{{ $group->creator->username ?? 'Không rõ' }}</td>
                <td>
                    <a href="{{ route('admin.groups.show', $group->id) }}" class="btn btn-info">Chi tiết</a>
                    <form action="{{ route('admin.groups.destroy', $group->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm này? Hành động này không thể hoàn tác.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-between">
        <!-- Hiển thị phân trang -->
        <div>{{ $groups->appends(['search' => request()->query('search')])->links() }}</div>

        <!-- Hiển thị thông tin số nhóm trên tổng số nhóm -->
        <div>Hiển thị {{ $groups->count() }} nhóm trên tổng {{ $groups->total() }}</div>
    </div>

    @endif
</div>
@endsection