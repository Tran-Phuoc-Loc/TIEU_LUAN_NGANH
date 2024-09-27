@extends('layouts.users')

@section('content')
<div class="welcome-contents">
    <h1>Bài Viết Đã Xuất Bản</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($posts->count() === 0) <!-- Kiểm tra có bài viết nào không -->
        <p>Hiện tại bạn chưa có bài viết nào đã xuất bản.</p>
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
                        <td>{{ $post->title }}</td>
                        <td>{{ Str::limit($post->content, 50) }}</td>
                        <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $post->category_id->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Thêm phân trang -->
        {{ $posts->links() }}
    @endif
</div>
@endsection