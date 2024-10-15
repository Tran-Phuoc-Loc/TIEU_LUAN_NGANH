@extends('layouts.users')

@section('content')
<div class="row">
    <div class="post-container">
        <h1>Bài Viết Đã Xuất Bản</h1>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if($published->isEmpty())
        <p>{{ $isCurrentUser ? 'Bạn chưa có bài viết nào đã xuất bản.' : 'Hiện tại người dùng này chưa có bài viết nào đã xuất bản.' }}</p>
        @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tiêu Đề</th>
                        <th>Nội Dung</th>
                        <th>Ngày Xuất Bản</th>
                        <th>Danh Mục</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($published as $post)
                    <tr>
                        <td><a href="{{ route('users.index', $post->id) }}">{{ $post->title }}</a></td>
                        <td>{{ Str::limit($post->content, 50) }}</td>
                        <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $post->category->name ?? 'Không có danh mục' }}</td>
                        <td>{{ ucfirst($post->status) }}</td> <!-- Hiển thị trạng thái với chữ cái đầu in hoa -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection