@extends('layouts.users')

@section('content')
<div class="welcome-contents">
    <h1>Bài Viết Đã Lưu</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($savedPosts->isEmpty())
        <p>Không có bài viết nào đã lưu.</p>
    @else
        <ul class="list-group">
            @foreach($savedPosts as $savedPost)
                <li class="list-group-item">
                    <a href="{{ route('posts.show', $savedPost->post->id) }}">{{ $savedPost->post->title }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection