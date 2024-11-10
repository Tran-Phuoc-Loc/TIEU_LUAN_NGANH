@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Quản lý bài viết</h2>

    <!-- Form tìm kiếm và bộ lọc -->
    <form method="GET" action="{{ route('admin.posts.index') }}">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tiêu đề...">
            </div>
            <div class="col-md-3">
                <select name="author" class="form-control">
                    <option value="">Tất cả tác giả</option>
                    @foreach($authors as $author)
                    <option value="{{ $author->id }}">{{ $author->username }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">Tất cả trạng thái</option>
                    <option value="draft">Nháp</option>
                    <option value="published">Đã đăng</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
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