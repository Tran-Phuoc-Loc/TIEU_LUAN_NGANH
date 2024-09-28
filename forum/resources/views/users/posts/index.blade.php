@extends('layouts.users')


@section('content')
<div class="welcome-contents">
    <div class="container">
        <h2>Tìm kiếm bài viết</h2>

        <h2>Kết quả tìm kiếm cho: "{{ request('query') }}"</h2>
        @if($posts->isEmpty())
        <p>Không tìm thấy bài viết nào.</p>
        @else
        <ul class="list-group">
            @foreach($posts as $post)
            <li class="list-group-item">
                <a href="{{ route('users.index', $post->id) }}">{{ $post->title }}</a>
                <p class="post-content">{{ Str::limit($post->content, 100) }}</p>
            </li>
            @endforeach
        </ul>
        @endif
        <!-- Hiển thị kết quả tìm kiếm người dùng -->
        @if($users->isNotEmpty())
            <h3>Kết quả tìm kiếm người dùng:</h3>
            <ul class="list-group">
                @foreach($users as $user)
                    <li class="list-group-item">
                        <a href="{{ route('users.profile.index', $user->id) }}">{{ $user->username }}</a> <!-- Liên kết đến trang hồ sơ người dùng -->
                    </li>
                @endforeach
            </ul>
        @else
            <p>Không tìm thấy người dùng nào.</p>
        @endif
    </div>
</div>
@endsection