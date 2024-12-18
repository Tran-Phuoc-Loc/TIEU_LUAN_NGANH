@extends('layouts.users')

@section('title', 'Chi tiết nhóm')

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
        @if($group->posts->isEmpty())
        <p>Nhóm này chưa đăng tải bài viết nào.</p>
        @else
        @foreach($group->posts as $post)
        @if($post->group)
        @php
        // Kiểm tra xem bài viết có thuộc nhóm riêng tư không và người dùng có thuộc nhóm đó không
        $isGroupPostVisible = $post->group->visibility == 'public' || ($user && $user->groups->contains($post->group));
        @endphp

        @if(!$isGroupPostVisible)
        <!-- Hiển thị cảnh báo nếu bài viết thuộc nhóm riêng tư và người dùng chưa tham gia -->
        <div class="alert alert-warning">
            <i class="fas fa-lock"></i> Bài viết này thuộc nhóm riêng tư. Hãy tham gia nhóm để xem nội dung.
        </div>
        @else
        <div class="post-card">
            <div class="post-meta d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center">
                    <a href="{{ route('users.profile.index', ['user' => $post->user->id]) }}">
                        <img
                            src="{{ $post->user->profile_picture ? (filter_var($post->user->profile_picture, FILTER_VALIDATE_URL) ? $post->user->profile_picture : asset('storage/' . $post->user->profile_picture)) : asset('storage/images/avataricon.png') }}"
                            alt="Avatar of {{ $post->user->username }}"
                            class="post-avatar"
                            loading="lazy">
                    </a>
                    <span class="post-author">Đăng bởi: <strong style="color: #000;">{{ $post->user->username }}</strong></span> |
                    <span class="post-time">
                        @if($post->published_at)
                        {{ $post->published_at->isoFormat('MMM Do YYYY, h:mm ') }}
                        @else
                        {{ $post->created_at->isoFormat('MMM Do YYYY, h:mm ') }}
                        @endif
                    </span>
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
        @endif
        @endforeach
        @endif
    </div>
</div>
<div class="col-lg-3 col-md-3 mt-lg-0 right-sidebar" style="background-color: #fff; width: 32%; margin-left: auto;">
    @if(auth()->check() && auth()->user()->groups->contains($group))
    <!-- Nút Viết bài -->
    <a href="{{ route('users.posts.create', ['groupId' => $group->id]) }}" class="btn btn-success">
        <i class="fas fa-file-pen"></i>
        <span class="d-none d-lg-inline">Viết bài</span>
    </a>
    @endif
    <div class="post-container mb-4">
        <div class="row">
            <!-- Hiển thị avatar nhóm bên trái -->
            <div class="d-flex align-items-center">
                <img src="{{ asset('storage/' . ($group->avatar ?? 'groups/avatars/group_icon.png')) }}" alt="Avatar của nhóm {{ $group->name }}" class="rounded thumbnail" style="width: 80px; height: 100px; margin-right: 15px;">
                <h1>{{ $group->name }}</h1>
            </div>
            @if ($group->creator_id === Auth::id())
            <div class="d-flex mt-3">
                <!-- Nút chỉnh sửa nhóm -->
                <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-warning mr-2">Chỉnh sửa nhóm</a>

                <!-- Nút xóa nhóm -->
                <form action="{{ route('groups.destroy', $group->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')" class="btn btn-danger">Xóa nhóm</button>
                </form>
            </div>
            @endif
            <p>Số lượng thành viên: {{ $group->members->count() }}</p>
            <p><strong>Nội Dung:</strong> {{ $group->description }}</p>
            <p><strong>Người tạo:</strong> {{ $group->creator->username ?? 'Không rõ' }}</p>
            <p><strong>Ngày tạo:</strong> {{ $group->created_at->format('d/m/Y H:i') }}</p>

            <!-- Thêm trạng thái nhóm -->
            @if($group->visibility)
            <p>Trạng thái nhóm: Cần phê duyệt tham gia</p>
            @else
            <p>Trạng thái nhóm: Mở (Không cần phê duyệt tham gia)</p>
            @endif

            @php
            $isMember = $group->members()->where('user_id', Auth::id())->exists();
            $hasRequested = $group->memberRequests()->where('user_id', Auth::id())->exists();
            @endphp

            @if(Auth::id() !== $group->creator_id) <!-- Kiểm tra nếu người dùng không phải là người tạo -->
            @if($group->visibility)
            @if(!$isMember && !$hasRequested)
            <form action="{{ route('groups.join', $group->id) }}" method="POST">
                @csrf
                <button type="submit">Yêu Cầu Tham Gia Nhóm</button>
            </form>
            @elseif($hasRequested && $group->memberRequests()->where('user_id', Auth::id())->where('status', 'pending')->exists())
            <p>Bạn đã yêu cầu tham gia nhóm này. Vui lòng chờ sự phê duyệt từ chủ nhóm.</p>
            @else
            <p>Bạn đã là thành viên của nhóm này.</p>
            @endif
            @else
            @if(!$isMember)
            <form action="{{ route('groups.join', $group->id) }}" method="POST">
                @csrf
                <button type="submit">Tham Gia Nhóm</button>
            </form>
            @else
            <form action="{{ route('groups.leave', $group->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit">Rời Nhóm</button>
            </form>
            <p>Bạn đã là thành viên của nhóm này.</p>
            @endif
            @endif
            @else
            <p>Xin chào chủ Group</p>
            @endif

            <!-- Kiểm tra nếu người dùng là người tạo nhóm -->
            @if(Auth::id() === $group->creator_id)
            <form action="{{ route('groups.destroy', $group->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')">Xóa Nhóm</button>
            </form>
            @endif

            <!-- Hiển thị danh sách thành viên -->
            <h3>Thành viên trong nhóm:</h3>
            <div class="row">
                @foreach ($group->members as $user)
                <div class="col-6 col-md-4 col-lg-3 mb-3 text-center">
                    <a href="{{ route('users.profile.index', ['user' => $user->id]) }}">
                        <!-- Avatar người dùng -->
                        <img src="{{ $user->profile_picture ? 
                 (filter_var($user->profile_picture, FILTER_VALIDATE_URL) 
                 ? $user->profile_picture 
                 : asset('storage/' . $user->profile_picture)) 
                 : asset('storage/images/avataricon.png') }}" 
                 alt="Avatar của {{ $user->username }}" class="rounded-circle" style="width: 40px; height: 40px;">

                    </a>

                    <!-- Nếu người dùng là chủ nhóm và không phải chính mình, cho phép đuổi người khỏi nhóm -->
                    @if(Auth::id() === $group->creator_id && Auth::id() !== $user->id)
                    <form action="{{ route('groups.kick', [$group->id, $user->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Bạn có chắc chắn muốn đuổi người này ra khỏi nhóm?')">Kick Rời nhóm</button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Hiển thị yêu cầu tham gia (dành cho chủ nhóm) -->
            @if(Auth::id() === $group->creator_id)
            <h3>Các yêu cầu tham gia:</h3>
            <ul class="list-unstyled overflow-auto" style="max-height: 200px;">
                @foreach ($group->joinRequests()->where('status', 'pending')->get() as $request)
                <li>
                    {{ $request->user->username }}
                    <form action="{{ route('groups.approve', [$group->id, $request->user_id]) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">Duyệt</button>
                    </form>
                    <form action="{{ route('groups.reject', [$group->id, $request->user_id]) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Từ chối</button>
                    </form>
                </li>
                @endforeach
            </ul>
            @endif

            <!-- Hiển thị bài viết trong nhóm (nếu có) -->
            @if($group->posts->isNotEmpty())
            <h3>Bài viết trong nhóm:</h3>
            <ul class="list-unstyled overflow-auto" style="max-height: 200px;">
                @foreach ($group->posts as $post)
                <li>{{ $post->title }}</li>
                @endforeach
            </ul>
            @else
            <p>Nhóm này chưa đăng tải bài viết nào.</p>
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