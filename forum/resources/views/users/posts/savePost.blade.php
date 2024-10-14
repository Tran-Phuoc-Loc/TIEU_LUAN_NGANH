@extends('layouts.users')

@section('content')
<div class="welcome-contents">
    <h1>Bài Viết Đã Lưu Theo Thư Mục</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($folders->isEmpty())
        <p>Không có thư mục nào hoặc không có bài viết nào đã lưu.</p>
    @else
        @foreach($folders as $folder)
            <div class="folder-section">
                <h3>Thư mục: {{ $folder->name }}</h3>

                @if($folder->savedPosts->isEmpty())
                    <p>Thư mục này chưa có bài viết nào.</p>
                @else
                    <ul class="list-group">
                        @foreach($folder->savedPosts as $savedPost)
                            <li class="list-group-item">
                                <a href="{{ route('users.folders.index', $savedPost->post->id) }}">{{ $savedPost->post->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection