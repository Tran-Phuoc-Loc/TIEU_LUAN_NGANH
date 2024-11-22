@extends('layouts.admin')

@section('content')
<h1>Quản lý bài viết diễn đàn</h1>
<div class="mb-3">
    <a href="{{ route('admin.forum.categories.index') }}" class="btn btn-primary">Quản lý danh mục</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Người tạo</th>
            <th>Danh mục</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($posts as $post)
        <tr>
            <td>{{ $post->id }}</td>
            <td>{{ $post->title }}</td>
            <td>{!! Str::limit(strip_tags($post->content), 100) !!}</td>
            <td>{{ $post->user->username }}</td>
            <td>{{ $post->category->name }}</td>
            <td>
                <a href="{{ route('admin.forum.show', $post->id) }}" class="btn btn-info">Xem</a>
                <form action="{{ route('admin.forum.destroy', $post->id) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="d-flex justify-content-between">
    <!-- Hiển thị phân trang -->
    <div>{{ $posts->appends(['search' => request()->query('search')])->links() }}</div>

    <!-- Hiển thị thông tin số nhóm trên tổng số nhóm -->
    <div>Hiển thị {{ $posts->count() }} bài viết trên tổng {{ $posts->total() }}</div>
</div>
@endsection