@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Quản lý bài viết</h2>

    <form method="GET" action="{{ route('admin.posts.index') }}">
        <div class="row">
            <!-- Tìm kiếm tiêu đề -->
            <div class="col-md-4 mb-3">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tiêu đề..." value="{{ request('search') }}">
            </div>

            <!-- Chọn tác giả -->
            <div class="col-md-4 mb-3">
                <select name="author" class="form-control">
                    <option value="">Tất cả tác giả</option>
                    @foreach($authors as $author)
                    <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>
                        {{ $author->username }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Chọn trạng thái -->
            <div class="col-md-4 mb-3">
                <select name="status" class="form-control">
                    <option value="">Tất cả trạng thái</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã đăng</option>
                </select>
            </div>

            <!-- Ngày bắt đầu -->
            <div class="col-md-4 mb-3">
            <label for="start_date" class="form-label">Ngày bắt đầu</label>
                <input type="date" name="start_date" class="form-control" placeholder="Ngày bắt đầu" value="{{ request('start_date') }}">
            </div>

            <!-- Ngày kết thúc -->
            <div class="col-md-4 mb-3">
            <label for="start_date" class="form-label">Ngày kết thúc</label>
                <input type="date" name="end_date" class="form-control" placeholder="Ngày kết thúc" value="{{ request('end_date') }}">
            </div>

            <!-- Nút tìm kiếm -->
            <div class="col-md-4 mb-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
            </div>
        </div>
    </form>

    <!-- Hiển thị thông báo -->
    @if($message)
    <div class="alert alert-warning">
        {{ $message }}
    </div>
    @endif

    <!-- Bảng liệt kê bài viết -->
    <form action="{{ route('admin.posts.bulkAction') }}" method="POST">
        @csrf
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="select-all"> <!-- Chọn tất cả -->
                    </th>
                    <th>Tiêu đề</th>
                    <th>Tác giả</th>
                    <th>Trạng thái</th>
                    <th>Loại bài viết</th> <!-- Cột mới cho loại bài viết -->
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $post)
                <tr>
                    <td>
                        <input type="checkbox" name="post_ids[]" value="{{ $post->id }}">
                    </td>
                    <td>{{ $post->title }}</td>
                    <td>{{ optional($post->author)->username ?? 'Tác giả không tồn tại' }}</td>
                    <td>{{ ucfirst($post->status) }}</td>
                    <td>
                        @if($post->group_id)
                        <!-- Nếu bài viết có group_id, hiển thị tên nhóm -->
                        {{ $post->group->name }}
                        @else
                        <!-- Nếu không có group_id, hiển thị "Cá nhân" -->
                        Cá nhân
                        @endif
                    </td>
                    <td>{{ $post->created_at->format('d-m-Y') }}</td>
                    <td>
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-warning btn-sm">Chỉnh sửa</a>
                        <button type="submit" formaction="{{ route('admin.posts.destroy', $post->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">Xóa</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Hành động hàng loạt -->
        <div class="row">
            <div class="col-md-6">
                <select name="bulk_action" class="form-control">
                    <option value="">Chọn hành động</option>
                    <option value="delete">Xóa bài</option>
                </select>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary">Áp dụng</button>
            </div>
        </div>
    </form>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-4">
        {{ $posts->links() }}
    </div>

    <script>
        // Chức năng "Chọn tất cả"
        document.getElementById('select-all').onclick = function() {
            var checkboxes = document.querySelectorAll('input[name="post_ids[]"]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
    </script>

    @endsection