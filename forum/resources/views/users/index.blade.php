@extends('layouts.users')

@section('title', 'Danh sách bài viết')

@section('content')
<div class="row">
    <div class="post-container">
        @if(session('error'))
        <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="alert alert-danger text-center" style="width: 300px;">
                {{ session('error') }}
                <div class="mt-3">
                    <a href="{{ route('login') }}" class="btn btn-primary">Đăng Nhập</a>
                </div>
            </div>
        </div>
        @endif

        @if($posts->isEmpty())
        <p>Không có bài viết nào.</p>
        @else
            @foreach ($posts as $post)
            <div class="post-card">
                <div class="post-meta">
                <img src="{{ $post->user->avatar_url ? asset('storage/' . $post->user->avatar_url) : asset('storage/images/avataricon.png') }}" alt="Avatar" class="post-avatar">
                    <span class="post-author">Đăng bởi: <strong>{{ $post->user->username }}</strong></span> |
                    <span class="post-time">{{ $post->created_at->diffForHumans() }}</span>
                </div>
                <div class="vote-section">
                    <i class="fas fa-arrow-up"></i>
                    <span>{{ $post->votes_count }}</span>
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="post-content">
                    <div class="post-title">{{ $post->title }}</div>
                    <div class="post-description">{{ Str::limit($post->content, 100) }}</div>
                    @if($post->image_url)
                    <div class="post-image">
                        <img src="{{ asset('storage/' . $post->image_url) }}" alt="">
                    </div>
                    @endif
                    <div class="post-footer">
                        <div>
                            <span><i class="fas fa-comments"></i> {{ $post->comments }} bình luận</span> |
                            <a href="#" class="like-button"><i class="fas fa-heart"></i></a>
                            <button class="btn btn-link"><i class="fas fa-bookmark"></i> Lưu</button>
                            <button class="btn btn-link">Chia sẻ</button>
                            @if(auth()->check() && auth()->user()->id === $post->user_id) <!-- Kiểm tra quyền -->
                            <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning btn-sm">Chỉnh Sửa</a>
                            <form action="{{ route('posts.recall', $post->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Thu Hồi</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>
@endsection