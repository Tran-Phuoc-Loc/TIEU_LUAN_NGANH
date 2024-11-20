@extends('layouts.users')

@section('title', 'Danh sách danh mục')

@section('content')
@include('layouts.partials.sidebar')
<style>
    /* Ẩn các ảnh sau ảnh thứ 2 */
    .image-grid .image-item:nth-child(n+3) {
        display: none;
    }

    .post-images-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .image-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .image-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        width: 100%;
        aspect-ratio: 1;
        /* Khung hình vuông */
    }

    .image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        border-radius: 8px;
    }

    /* Hiển thị số lượng ảnh còn lại */
    .more-images-overlay {
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: rgba(0, 0, 0, 0.6);
        color: #fff;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
</style>
<div class="col-lg-6 col-md-7 offset-lg-2 content-col" style="border: 2px solid #007bff; background-color:#fff; margin-left: 17%;">
    <div class="post-container">
        <h1 class="text-center">Bài viết trong danh mục: {{ $category->name }}</h1>
        <!-- Thêm phần lọc -->
        <div class="filter-buttons mb-3 d-flex gap-2">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'new']) }}"
                class="btn {{ request('sort') == 'new' ? 'btn-primary' : 'btn-outline-primary' }}">
                Mới nhất
            </a>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'hot']) }}"
                class="btn {{ request('sort') == 'hot' ? 'btn-primary' : 'btn-outline-primary' }}">
                Nổi bật
            </a>
            <a href="{{ request()->url() }}"
                class="btn {{ !request('sort') ? 'btn-primary' : 'btn-outline-primary' }}">
                Mặc định
            </a>
        </div>
        @if(isset($group) && $group->posts->isNotEmpty())
        <p>Không có bài viết nào.</p>
        @else

        @foreach ($posts as $post)
        @if($post->status == 'published')
        <div class="post-card">
            <div class="post-meta d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center">
                    <a href="{{ route('users.profile.index', ['user' => $post->user->id]) }}">
                        <img src="{{ $post->user->profile_picture ? (filter_var($post->user->profile_picture, FILTER_VALIDATE_URL) ? $post->user->profile_picture : asset('storage/' . $post->user->profile_picture)) : asset('storage/images/avataricon.png') }}" alt="Avatar" class="post-avatar" loading="lazy">
                    </a>
                    <span class="post-author">Đăng bởi: <strong style="color: #000;">{{ $post->user->username }}</strong></span> |
                    <span class="post-time">
                        @if($post->published_at)
                        {{ $post->published_at->isoFormat('MMM Do YYYY, h:mm ') }}
                        @else
                        {{ $post->created_at->isoFormat('MMM Do YYYY, h:mm ') }}
                        @endif
                    </span>
                    <!-- Hiển thị tên nhóm nếu có group_id -->
                    @if($post->group_id)
                    |<span class="group-name">Nhóm:
                        <a href="{{ route('users.groups.show', ['id' => $post->group_id]) }}">
                            <strong>{{ $post->group->name }}</strong>
                        </a>
                    </span>
                    @endif
                </div>

                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        •••
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <!-- Nút Báo vi phạm chỉ hiện nếu người dùng hiện tại không phải là tác giả bài viết -->
                        @if(auth()->check() && auth()->user()->id !== $post->user_id)
                        <li>
                            <button class="dropdown-item report-button" data-post-id="{{ $post->id }}" style="color: red;">
                                Báo vi phạm
                            </button>
                        </li>
                        @endif
                        <!-- Các tùy chọn khác chỉ hiện cho tác giả bài viết -->
                        @if(auth()->check() && auth()->user()->id === $post->user_id)
                        <li>
                            <a href="{{ route('posts.edit', $post->id) }}" class="dropdown-item btn btn-warning btn-sm"><i class="bi bi-brush-fill"></i> Chỉnh Sửa</a>
                        </li>
                        <li>
                            <form action="{{ route('posts.recall', $post->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="dropdown-item btn btn-dark btn-sm"><i class="bi bi-arrow-counterclockwise"></i> Thu Hồi</button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item btn btn-dark btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');"><i class="bi bi-x-circle-fill"></i> Xóa Bài Viết</button>
                            </form>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="post-category mt-1">
                @if($post->category)
                <span>
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
                    <span class="content-preview">{!! Str::limit($post->content, 100) !!}</span>
                    <span class="content-full" style="display: none;">{!! $post->content !!}</span>
                </div>
                @if (strlen($post->content) > 100)
                <button class="btn btn-link toggle-content">Xem thêm</button>
                @endif

                <div class="post-media">
                    <!-- Kiểm tra và hiển thị ảnh hoặc video chính -->
                    @if($post->image_url)
                    @if($post->isImage())
                    <div class="post-image">
                        <img src="{{ asset('storage/' . $post->image_url) }}" alt="Post Image"
                            class="img-fluid"
                            data-post-id="{{ $post->id }}"
                            data-image-url="{{ asset('storage/' . $post->image_url) }}"
                            onclick="openModal(this)">
                    </div>
                    @elseif($post->isVideo())
                    <div class="post-video">
                        <video class="video-player" controls>
                            <source src="{{ asset('storage/public/' . $post->image_url) }}" type="video/mp4">
                            Trình duyệt của bạn không hỗ trợ video.
                        </video>
                    </div>
                    @endif
                    @endif

                    <!-- Hiển thị nhiều ảnh phụ từ bảng post_images -->
                    @if($post->postImages && $post->postImages->isNotEmpty())
                    <div class="post-images-gallery">
                        <div class="image-grid">
                            @foreach ($post->postImages as $index => $image)
                            <div class="image-item">
                                <img src="{{ asset('storage/' . $image->file_path) }}"
                                    alt="Post Image"
                                    data-post-id="{{ $post->id }}"
                                    data-image-url="{{ asset('storage/' . $image->file_path) }}"
                                    class="post-thumbnail"
                                    onclick="openModal(this)">

                                <!-- Nút "Xem thêm" cho ảnh số 2 -->
                                @if($index === 1 && $post->postImages->count() > 2)
                                <div class="more-images-overlay">+{{ $post->postImages->count() - 2 }} Xem thêm</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>

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
<div class="col-lg-3 col-md-3 mt-lg-0 right-sidebar" style="background-color: #fff; width: 32%; margin-left: auto;">
<div class="post-container mb-4">
        <div class="row">
            <h1 class="text-center">Danh Sách Danh Mục</h1>

            @if ($categories->isEmpty())
            <div class="empty-message">
                <p>Không có danh mục nào.</p>
            </div>
            @else
            <ul class="list-group">
                @foreach ($categories as $category)
                <li class="list-group-item category-item">
                    <a href="{{ route('categories.posts', ['slug' => $category->slug]) }}" class="category-link">
                        {{ $category->name }}
                    </a>
                </li>
                @endforeach
            </ul>
            @endif
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
    @if(auth()->check() && isset($post) && $post->id)
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

@if(isset($post) && $post->id)
<!-- Form ẩn để gửi báo cáo -->
<form id="reportForm-{{ $post->id }}" action="{{ route('admin.reports.store') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="post_id" value="{{ $post->id }}">
    <input type="hidden" name="reason" id="reasonInput-{{ $post->id }}" value="">
</form>
@else
<p class="text-danger">Không thể gửi báo cáo vì bài viết không tồn tại.</p>
@endif
@endsection