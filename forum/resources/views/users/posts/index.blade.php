@extends('layouts.users')

@section('title', 'Danh sách tìm kiếm')

@section('content')
@include('layouts.partials.sidebar')

<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
            <h2>Kết quả tìm kiếm cho: "{{ request('query') }}"</h2>

            <!-- Kết quả tìm kiếm bài viết diễn đàn -->
            <h3>Bài Viết Diễn Đàn</h3>
            @if($forumPosts->isEmpty())
                <p>Không tìm thấy bài viết diễn đàn nào.</p>
            @else
                <ul class="list-group mb-4">
                    @foreach($forumPosts as $post)
                    <li class="list-group-item">
                        <a href="{{ route('forums.show', $post->id) }}">{{ $post->title }}</a>
                        <p>{{ Str::limit($post->content, 100) }}</p>
                        <small>Viết bởi: {{ $post->user->username ?? 'Không có tên' }} - {{ $post->created_at->format('d-m-Y') }}</small>
                    </li>
                    @endforeach
                </ul>
            @endif

            <!-- Kết quả tìm kiếm bài viết mạng xã hội -->
            <h3>Bài Viết Mạng Xã Hội</h3>
            @if($posts->isEmpty())
                <p>Không tìm thấy bài viết nào.</p>
            @else
                <ul class="list-group mb-4">
                    @foreach($posts as $post)
                    <li class="list-group-item">
                        <a href="{{ route('users.index', $post->id) }}">{{ $post->title }}</a>
                        <p>{{ Str::limit($post->content, 100) }}</p>
                        <small>Viết bởi: {{ $post->user->username ?? 'Không có tên' }} - {{ $post->created_at->format('d-m-Y') }}</small>
                    </li>
                    @endforeach
                </ul>
            @endif

            <!-- Kết quả tìm kiếm người dùng -->
            <h3>Người Dùng</h3>
            @if($users->isNotEmpty())
                <ul class="list-group mb-4">
                    @foreach($users as $user)
                    <li class="list-group-item">
                        <a href="{{ route('users.profile.index', $user->id) }}">{{ $user->username }}</a>
                    </li>
                    @endforeach
                </ul>
            @else
                <p>Không tìm thấy người dùng nào.</p>
            @endif

            <!-- Kết quả tìm kiếm nhóm -->
            <h3>Nhóm</h3>
            @if($groups->isNotEmpty())
                <ul class="list-group">
                    @foreach($groups as $group)
                    <li class="list-group-item">
                        <a href="{{ route('users.groups.show', $group->id) }}">{{ $group->name }}</a>
                    </li>
                    @endforeach
                </ul>
            @else
                <p>Không tìm thấy nhóm nào.</p>
            @endif
        </div>
    </div>
</div>
@endsection
