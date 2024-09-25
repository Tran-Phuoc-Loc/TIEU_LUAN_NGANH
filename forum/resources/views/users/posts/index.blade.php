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
</div>
@endsection