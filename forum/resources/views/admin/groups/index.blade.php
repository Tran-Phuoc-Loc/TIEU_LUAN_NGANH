@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Quản lý Group</h1>

    <!-- Form tìm kiếm -->
    <form action="{{ route('admin.groups.index') }}" method="GET" class="mb-4">
        <div class="input-group" >
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm nhóm..." value="{{ request()->query('search') }}" aria-label="Tìm kiếm nhóm" aria-describedby="search-button">
            <button class="btn btn-primary" type="submit" id="search-button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Group</th>
                <th>Mô tả</th>
                <th>Chủ nhóm</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if($groups->isEmpty())
                <tr>
                    <td colspan="5" class="text-center">Không tìm thấy kết quả nào.</td>
                </tr>
            @else
                @foreach($groups as $group)
                    <tr>
                        <td>{{ $group->id }}</td>
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
            @endif
        </tbody>
    </table>

    <!-- Phân trang nếu có -->
    {{ $groups->links() }}

</div>
@endsection
