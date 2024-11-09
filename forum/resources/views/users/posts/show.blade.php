@extends('layouts.users')

@section('content')
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
            @if(!empty($post->edit_reason))
            <div class="alert alert-warning">
                Bài viết này cần được sửa đổi. <br>
                Lý do: {{ $post->edit_reason }}
            </div>
            @endif

            <h1>{{ $post->title ?? 'Không có bài viết cần chỉnh sửa' }}</h1>
            <p>{{ $post->content ?? '' }}</p>
            <p><strong>Danh mục:</strong> {{ $post->category->name ?? '' }}</p>
            <p><strong>Tác giả:</strong> {{ $post->user->username ?? '' }}</p>
            <p><strong>Trạng thái:</strong> {{ $post->status ?? '' }}</p>

            <a href="{{ route('users.index') }}">Quay lại danh sách bài viết</a>
            <!-- Thêm link đến trang chỉnh sửa bài viết -->
            <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning">Chỉnh sửa bài viết</a>
        </div>
        @endsection