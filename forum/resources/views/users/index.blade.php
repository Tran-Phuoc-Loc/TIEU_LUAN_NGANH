@extends('layouts.users')

@section('title', 'Danh sách bài viết')

@section('content')
<div class="row">
    <div class="post-container">
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

            <div class="post-content">
                <div class="post-title">{{ $post->title }}</div>
                <div class="post-description">{{ Str::limit($post->content, 100) }}</div>

                @if($post->image_url)
                <div class="post-image">
                    <img src="{{ asset('storage/' . $post->image_url) }}" alt="">
                </div>
                @endif

                <div class="post-footer">
                    <div class="post-actions">
                        <span class="comment-toggle" style="cursor:pointer;">
                            {{ $post->comments_count }}
                        </span>
                        <span class="comment-toggle" style="cursor:pointer;">
                            <i class="fas fa-comment-dots"></i> Bình luận
                        </span>
                        <a href="#" class="like-button"><i class="fas fa-heart"></i></a>
                        <button class="btn btn-link"><i class="fas fa-bookmark"></i> Lưu</button>
                        <button class="btn btn-link">Chia sẻ</button>
                    </div>

                    @if(auth()->check() && auth()->user()->id === $post->user_id)
                    <div class="post-management">
                        <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning btn-sm">Chỉnh Sửa</a>
                        <form action="{{ route('posts.recall', $post->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-dark btn-sm">Thu Hồi</button>
                        </form>
                    </div>
                    @endif

                    @if(auth()->check())
                    <div class="comment-form" style="display:none;">
                        <textarea class="form-control mt-2" rows="3" placeholder="Nhập bình luận của bạn"></textarea>
                        <button class="btn btn-primary mt-2">Gửi bình luận</button>
                    </div>
                    @else
                    @endif
                </div> <!-- Kết thúc .post-footer -->
            </div> <!-- Kết thúc .post-content -->
        </div> <!-- Kết thúc .post-card -->
        @endforeach
        @endif
    </div>

    <!-- Modal Đăng Nhập -->
    <div class="modal" id="loginModal" style="display:none;">
        <div class="modal-content">
            <span class="close" style="cursor:pointer;">&times;</span>
            <h5>Bạn cần đăng nhập</h5>
            <p>Vui lòng đăng nhập để có thể bình luận.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Đăng Nhập</a>
        </div>
    </div>
    @endsection