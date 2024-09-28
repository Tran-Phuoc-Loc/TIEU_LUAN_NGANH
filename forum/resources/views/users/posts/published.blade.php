@extends('layouts.users')

@section('content')
<div class="welcome-contents">
    <h1>Bài Viết Đã Xuất Bản </h1> <!-- Hiển thị tên người dùng -->

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($posts->count() === 0) <!-- Kiểm tra có bài viết nào không -->
        <p>Hiện tại người dùng này chưa có bài viết nào đã xuất bản.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Tiêu Đề</th>
                    <th>Nội Dung</th>
                    <th>Ngày Xuất Bản</th>
                    <th>Danh mục</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr>
                        <td><a href="{{ route('users.index', $post->id) }}">{{ $post->title }}</a></td> <!-- Thêm liên kết đến bài viết -->
                        <td>{{ Str::limit($post->content, 50) }}</td>
                        <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $post->category ? $post->category->name : 'Không có' }}</td> <!-- Kiểm tra và lấy tên danh mục -->
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Thêm phân trang -->
        {{ $posts->links() }}
    @endif
</div>
@endsection
