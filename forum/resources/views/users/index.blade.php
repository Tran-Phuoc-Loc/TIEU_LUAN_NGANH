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
                        <button class="like-button" data-post-id="{{ $post->id }}">
                            <i class="far fa-thumbs-up fa-lg"></i> <span class="like-count">{{ $post->like_count }}</span>
                        </button>
                        <span class="comment-toggle" style="cursor:pointer;" data-post-id="{{ $post->id }}">
                            <i class="fas fa-comment-dots"></i> Xem Bình Luận ({{ $post->comments_count }})
                        </span>
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
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>

<!-- Modal Bình Luận -->
<div class="modal" id="commentModal" style="display:none;">
    <div class="modal-content">
        <span class="close" style="cursor:pointer;">&times;</span>
        <div class="modal-body">
            <h5 id="modalPostTitle">Bình luận cho bài viết</h5>
            <div class="comments-list">
                @if(isset($comments) && $comments->count() > 0)
                @foreach($comments as $comment)
                <div class="comment">
                    <img src="{{ $comment->user->avatar_url ? asset('storage/' . $comment->user->avatar_url) : asset('storage/images/avataricon.png') }}" alt="Avatar" class="comment-avatar" loading="lazy">
                    <strong>{{ $comment->user->username }}</strong>:
                    <small>
                        {{ $comment->created_at->isoFormat('DD/MM/YYYY HH:mm') }}
                        ({{ $comment->created_at->diffForHumans() }})
                    </small>
                    <h6>{{ $comment->content }}</h6>
                    @if($comment->image_url)
                    <div class="comment-image">
                        <img src="{{ asset('storage/' . $comment->image_url) }}" alt="Comment Image" loading="lazy">
                    </div>
                    @endif
                    <div class="comment-actions">
                        <button class="like-button" data-comment-id="${comment.id}">
                            <i class="far fa-thumbs-up"></i> <span class="like-count">${comment.likes_count}</span>
                        </button>
                        <button class="share-button" data-comment-id="{{ $comment->id }}">
                            <i class="fas fa-share-alt"></i> Chia sẻ
                        </button>
                        <button class="relay-button" data-comment-id="{{ $comment->id }}">
                            <i class="fas fa-retweet"></i> Relay
                        </button>
                    </div>
                </div>
                @endforeach
                @else
                <p>Chưa có bình luận nào.</p>
                @endif
            </div>
            @if(auth()->check())
            <form id="commentForm" action="{{ route('comments.store', $post->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: auto;">
                @csrf
                <div class="textarea-container">
                    <textarea name="content" class="form-control" rows="3" placeholder="Nhập bình luận của bạn" required></textarea>
                    <input type="file" name="image" class="file-input" accept="image/*" id="fileInput" style="display:none;">
                    <button type="button" class="file-icon" onclick="document.getElementById('fileInput').click();">
                        <i class="fas fa-upload"></i> <!-- Icon tải lên -->
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> <!-- Hình mũi tên -->
                    </button>
                </div>
            </form>
            @else
            <p>Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để bình luận.</p>
            @endif
        </div>
    </div>
</div>
<!-- Modal Đăng Nhập -->
<div class="modal" id="loginModal" style="display:none;">
    <div class="modal-content">
        <span class="close" style="cursor:pointer;">&times;</span>
        <div class="modal-body">
            <h5 id="modalTitle">Đăng Nhập</h5>
            <p id="modalMessage">Vui lòng đăng nhập để thực hiện hành động này.</p>
            <p>Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để bình luận.</p>
        </div>
    </div>
</div>

@endsection