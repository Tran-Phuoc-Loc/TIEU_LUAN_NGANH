@extends('layouts.users')

@section('content')
<div class="welcome-contents">
    <h1>{{ $post->title }}</h1>
    <p>{{ $post->content }}</p>
    <p><strong>Danh mục:</strong> {{ $post->category->name }}</p>
    <p><strong>Tác giả:</strong> {{ $post->user->name }}</p>
    <p><strong>Trạng thái:</strong> {{ $post->status }}</p>
    <!-- Các thông tin khác nếu cần -->

    <a href="{{ route('users.index') }}">Quay lại danh sách bài viết</a>
    <!-- Thêm link đến trang chỉnh sửa bài viết -->
    <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning">Chỉnh sửa bài viết</a>
</div>
@endsection
