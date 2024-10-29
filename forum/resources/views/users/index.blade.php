@extends('layouts.users')

@section('title', 'Danh sách bài viết')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Menu điều hướng cho màn hình lớn -->
        <div class="col-lg-2 col-md-1 sidebar d-none d-md-block" style="background-color: #fff; position: fixed; height: 100vh; overflow-y: auto;">
            <div class="vertical-navbar">
                <!-- Thông tin người dùng -->
                <div class="user-info text-center mb-4">
                    @if(auth()->check())
                    <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('storage/images/avataricon.png') }}"
                        alt="Profile picture of {{ auth()->user()->username }}"
                        class="rounded-circle" style="width: 45px; height: 50px;">
                    <h5 class="d-none d-lg-block">{{ auth()->user()->username }}</h5>
                    @endif
                </div>

                <nav class="navbar navbar-dark flex-column">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">
                                <i class="fas fa-house"></i>
                                <span class="d-none d-lg-inline">Trang chủ</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="bi bi-pencil"></i>
                                <span class="d-none d-lg-inline">Bài viết của bạn</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">
                                <i class="bi bi-folder"></i>
                                <span class="d-none d-lg-inline">Danh mục</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forums.index') }}">
                                <i class="bi bi-chat-dots"></i>
                                <span class="d-none d-lg-inline">Diễn đàn</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <hr class="my-4">

                <nav class="navbar navbar-dark flex-column">
                    <ul class="navbar-nav">
                        <li class="nav-item" style="padding-bottom: 10px;">
                            <a href="{{ route('users.posts.create') }}" class="btn btn-success">
                                <i class="fas fa-file-pen"></i>
                                <span class="d-none d-lg-inline">Viết bài</span>
                            </a>
                        </li>
                        <li class="nav-item" style="padding-bottom: 10px;">
                            <a href="{{ route('users.groups.create') }}" class="btn btn-success">
                                <i class="bi bi-people"></i>
                                <span class="d-none d-lg-inline">Tạo nhóm</span>
                            </a>
                        </li>
                        <li class="nav-item" style="text-align: center;">
                            @if ($groups->isNotEmpty())
                            @php $firstGroup = $groups->first(); @endphp
                            <a href="{{ route('groups.chat', $firstGroup->id) }}">
                                <i class="fas fa-comment-sms" style="font-size: 40px"></i>
                                <span class="d-none d-lg-inline">Tin nhắn</span>
                            </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Phần nội dung bài viết -->
        <div class="col-lg-7 col-md-7 offset-lg-2 content-col" style="border: 2px solid #007bff; background-color:#fff; margin-left: 17%;">
            <div class="post-container">
                @if($posts->isEmpty())
                <p>Không có bài viết nào.</p>
                @else
                @foreach ($posts as $post)
                @if($post->status == 'published')
                <div class="post-card">
                    <div class="post-meta d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('users.profile.index', ['user' => $post->user->id]) }}">
                                <img src="{{ $post->user->profile_picture ? asset('storage/' . $post->user->profile_picture) : asset('storage/images/avataricon.png') }}" alt="Avatar" class="post-avatar" loading="lazy">
                            </a>
                            <span class="post-author">Đăng bởi: <strong>{{ $post->user->username }}</strong></span> |
                            <span class="post-time">
                                @if($post->published_at)
                                {{ $post->published_at->isoFormat('MMM Do YYYY, h:mm a') }}
                                @else
                                {{ $post->created_at->isoFormat('MMM Do YYYY, h:mm a') }}
                                @endif
                            </span>
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                •••
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <button class="dropdown-item report-button" data-post-id="{{ $post->id }}" style="color: red;">Báo vi phạm</button>
                                </li>
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
                            <img src="{{ asset('storage/' . $post->image_url) }}" alt="{{ $post->title }}" loading="lazy">
                        </div>
                        @endif

                        <div class="post-footer">
                            <div class="post-actions">
                                <button class="like-button" data-post-id="{{ $post->id }}">
                                    <i class="far fa-thumbs-up fa-lg"></i> <span class="like-count">{{ $post->likes_count }}</span>
                                </button>
                                <span class="comment-toggle" style="cursor:pointer;" data-post-id="{{ $post->id }}">
                                    <i class="fas fa-comment-dots"></i> Xem Bình Luận ({{ $post->comments_count }})
                                </span>
                                @if (in_array($post->id, $savedPosts))
                                <button class="btn btn-outline-danger unsave-post" data-post-id="{{ $post->id }}">
                                    <i class="fas fa-bookmark"></i> Bỏ lưu
                                </button>
                                @else
                                <button class="btn btn-outline-primary save-post" data-post-id="{{ $post->id }}">
                                    <i class="fas fa-bookmark"></i> Lưu
                                </button>
                                @endif
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="shareDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-share-alt"></i> Chia sẻ
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="shareDropdown">
                                        <li><a class="dropdown-item share-facebook" href="#" data-url="{{ route('users.index', $post->id) }}"><i class="fab fa-facebook"></i> Facebook</a></li>
                                        <li><a class="dropdown-item share-twitter" href="#" data-url="{{ route('users.index', $post->id) }}"><i class="fab fa-twitter"></i> Twitter</a></li>
                                        <li><a class="dropdown-item share-linkedin" href="#" data-url="{{ route('users.index', $post->id) }}"><i class="fab fa-linkedin"></i> LinkedIn</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
                @endif
            </div>
        </div>

        <!-- Sidebar phải: Gợi ý người theo dõi -->
        <div class="col-lg-3 col-md-3 mt-lg-0 right-sidebar" style="background-color: #fff; position: fixed; right: 0; height: 100vh; overflow-y: auto; margin-left: auto;">
            <div class="right-sidebars p-3">
                <h3 class="sidebar-title">Gợi ý theo dõi</h3>
                <ul class="suggested-users-list list-unstyled">
                    @forelse ($usersToFollow as $user)
                    <li class="d-flex align-items-center mb-2 follow-item">
                        <img
                            src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('storage/images/avataricon.png') }}"
                            alt="Profile picture of {{ $user->username }}"
                            class="rounded-circle"
                            height="40"
                            width="40" />
                        <div class="info ms-2">
                            <h5 class="mb-0">{{ $user->username }}</h5>
                            <p class="mb-0 text-muted">{{ $user->role }}</p>
                        </div>
                        <div class="ms-auto">
                            <button class="btn btn-primary btn-sm follow-btn" data-user-id="{{ $user->id }}">Theo dõi</button>
                            <button class="btn btn-success btn-sm ms-1 friend-btn" data-user-id="{{ $user->id }}">Thêm bạn</button>
                        </div>
                    </li>
                    @empty
                    <p class="text-center">Không có người dùng nào để theo dõi.</p>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>
</div>

<!-- Modal Bình Luận -->
<div class="modal" id="commentModal" style="display:none;">
    <div class="modal-content">
        <span class="close" style="cursor:pointer;">&times;</span>
        <div class="modal-body">
            <h5 id="modalPostTitle">Bình luận cho bài viết</h5>
            <div class="comments-list" style="max-height: 400px; overflow-y: auto;">
                @if(isset($comments) && $comments->count() > 0)
                @foreach($comments as $comment)
                <div class="comment">
                    <img src="{{ $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : asset('storage/images/avataricon.png') }}" alt="Avatar" class="comment-avatar" loading="lazy">
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
                        <button class="share-button" data-comment-id="${comment.id}">
                            <i class="fas fa-share-alt"></i> Chia sẻ
                        </button>
                        <button class="reply-button" data-comment-id="${comment.id}">
                            <i class="fas fa-reply"></i> Trả lời
                        </button>
                    </div>
                    <div class="replies" id="replies-${comment.id}"></div> <!-- Khu vực để hiển thị các bình luận trả lời -->
                </div>
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
            <input type="hidden" id="parent_id" name="parent_id" value="0">
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