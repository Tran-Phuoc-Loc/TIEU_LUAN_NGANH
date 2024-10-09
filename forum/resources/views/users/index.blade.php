@extends('layouts.users')

@section('title', 'Danh sách bài viết')

@section('content')
<div class="row">
    <div class="post-container">
        @if($posts->isEmpty())
        <p>Không có bài viết nào.</p>
        @else
        @foreach ($posts as $post)
        @if($post->status == 'published') <!-- Kiểm tra nếu bài viết là dạng published -->
        <div class="post-card">
            <div class="post-meta d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center">
                    <a href="{{ route('users.profile.index', ['user' => $post->user->id]) }}">
                        <img src="{{ $post->user->profile_picture ? asset('storage/' . $post->user->profile_picture) : asset('storage/images/avataricon.png') }}" alt="Avatar" class="post-avatar">
                    </a>
                    <span class="post-author">Đăng bởi: <strong>{{ $post->user->username }}</strong></span> |
                    <span class="post-time">{{ $post->created_at->diffForHumans() }}</span>
                </div>

                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        •••
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li>
                            <button class="dropdown-item report-button" data-post-id="{{ $post->id }}" style="color: red;">Báo vi phạm</button>
                        </li>
                        <!-- Có thể thêm các hành động khác ở đây -->
                        @if(auth()->check() && auth()->user()->id === $post->user_id)
                        <li>
                            <a href="{{ route('posts.edit', $post->id) }}" class="dropdown-item btn btn-warning btn-sm">Chỉnh Sửa</a>
                        </li>
                        <li>
                            <form action="{{ route('posts.recall', $post->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="dropdown-item btn btn-dark btn-sm">Thu Hồi</button>
                            </form>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            <!-- danh mục -->
            <div class="post-category mt-1">
                @if($post->category)
                <span>Danh mục:
                    <a href="{{ route('categories.index', ['slug' => $post->category->slug]) }}">
                        <strong>{{ $post->category->name }}</strong>
                    </a>
                </span>
                @else
                <span>Không có danh mục</span>
                @endif
            </div>

            <!-- Nội dung bài viết -->
            <div class="post-content">
                <div class="post-title">{{ $post->title }}</div>
                <div class="post-description">
                    <span class="content-preview">{{ Str::limit($post->content, 100) }}</span>
                    <span class="content-full" style="display: none;">{{ $post->content }}</span>
                </div>
                @if (strlen($post->content) > 100)
                <button class="btn btn-link toggle-content">Xem thêm</button>
                @endif

                @if($post->image_url)
                <div class="post-image">
                    <img src="{{ asset('storage/' . $post->image_url) }}" alt="{{ $post->title }}">
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
                        <button class="btn btn-link save-post" data-post-id="{{ $post->id }}">
                            <i class="fas fa-bookmark"></i> Lưu
                        </button>
                        <button class="btn btn-link" id="sharePost">
                            Chia sẻ
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif <!-- Kết thúc kiểm tra status -->
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

<!-- Form ẩn để gửi báo cáo -->
<form id="reportForm-{{ $post->id }}" action="{{ route('admin.reports.store') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="post_id" value="{{ $post->id }}">
    <input type="hidden" name="reason" id="reasonInput-{{ $post->id }}" value="">
</form>


<!-- Modal để chọn hoặc tạo thư mục -->
<div class="modal fade" id="folderModal" tabindex="-1" aria-labelledby="folderModalLabel" aria-describedby="folderModalDescription" aria-hidden="true" style="z-index: index auto;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="folderModalLabel">Chọn Thư Mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body" id="folderModalDescription">
                <p>Vui lòng chọn một thư mục có sẵn hoặc tạo một thư mục mới để lưu bài viết.</p>
                <div class="mb-3">
                    <label for="folderSelect" class="form-label">Chọn thư mục</label>
                    <select id="folderSelect" class="form-select" aria-label="Chọn thư mục">
                        <option value="" disabled selected>Chọn thư mục</option>
                        @foreach($folders as $folder)
                        <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="newFolderName" class="form-label">Tạo thư mục mới</label>
                    <input type="text" id="newFolderName" class="form-control" placeholder="Tên thư mục mới" aria-describedby="newFolderHelp">
                    <small id="newFolderHelp" class="form-text">Nhập tên thư mục mới nếu bạn không muốn chọn thư mục có sẵn.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveToFolder">Lưu</button>
            </div>
        </div>
    </div>
</div>
@endsection