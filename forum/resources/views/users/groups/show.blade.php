@extends('layouts.users')

@section('title', 'Chi tiết nhóm')

@section('content')
<div class="welcome-contents">
    <h1>{{ $group->name }}</h1>
    <p>{{ $group->description }}</p>
    <p>Người tạo: {{ $group->creator->username ?? 'Không rõ' }}</p>
    <p>Ngày tạo: {{ $group->created_at->format('d/m/Y H:i') }}</p>

    <!-- Hiển thị danh sách thành viên -->
    <h3>Thành viên trong nhóm:</h3>
    <ul>
        @foreach ($group->users as $user)
            <li>{{ $user->username }}</li>
        @endforeach
    </ul>

    <!-- Hiển thị bài viết trong nhóm (nếu có) -->
    @if($posts->isNotEmpty())
        <h3>Bài viết trong nhóm:</h3>
        <ul>
            @foreach ($posts as $post)
                <li>{{ $post->title }}</li>
            @endforeach
        </ul>
    @else
        <p>Nhóm này chưa có bài viết nào.</p>
    @endif
</div>
@endsection
