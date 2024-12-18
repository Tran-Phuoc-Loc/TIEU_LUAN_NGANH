@extends('layouts.users')

<!-- Thiết lập tiêu đề trang mặc định -->
@section('title', isset($pageTitle) ? $pageTitle : 'Bài viết - Trang chủ')
@include('layouts.partials.sidebar')

<!-- Phần nội dung bài viết -->
<div class="col-lg-7 col-md-7 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container">
        <!-- Hiển thị thông báo "Bài viết của bạn" nếu user_posts=true -->
        @if(request('user_posts') == 'true')
        <h2>Bài viết của bạn</h2>
        @endif
        <!-- Thêm phần lọc -->
        <div class="filter-buttons my-3 d-flex gap-2">
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
        @if($posts->isEmpty() && (!isset($group) || $group->posts->isEmpty()))
        <p>Bạn chưa đăng bài viết nào.</p>
        @else
        @php
        // Xác định collection bài viết cần hiển thị
        $displayPosts = isset($group) ? $group->posts : $posts;
        @endphp

        @foreach($displayPosts as $post)
        @php
        $isGroupPostVisible = !$post->group_id ||
        ($post->group_id && $post->group->visibility == 'public') ||
        ($post->group_id && Auth::check() && $post->group && $post->group->members->contains(Auth::id()));
        @endphp

        @if(!isset($group) && !$isGroupPostVisible)
        @continue
        @elseif(isset($group) && !$isGroupPostVisible)
        <div class="alert alert-warning">
            <i class="fas fa-lock"></i> Bài viết này thuộc nhóm riêng tư. Hãy tham gia nhóm để xem nội dung.
        </div>
        @else
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
                    <span class="content-preview">{!! Str::limit(strip_tags($post->content), 100) !!}</span>
                    <span class="content-full" style="display: none;">{!! $post->content !!}</span>
                </div>
                @if (strlen(strip_tags($post->content)) > 100)
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
                        <video class="video-player" controls loading="lazy" preload="none">
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

<!-- Sidebar phải: Gợi ý người theo dõi và thông tin group -->
<div class="col-lg-3 col-md-3 mt-lg-0 right-sidebar" style="background-color: #fff; position: fixed; right: 0; bottom:0; height: calc(100vh - 102px); overflow-y: auto;">

    @if(isset($group))
    @if(auth()->check() && auth()->user()->groups->contains($group))
    <!-- Nút Viết bài -->
    <a href="{{ route('users.posts.create', ['groupId' => $group->id]) }}" class="btn btn-success mb-3">
        <i class="fas fa-file-pen"></i>
        <span class="d-none d-lg-inline">Viết bài</span>
    </a>
    @endif

    <div class="post-container mb-4">
        <div class="row">
            <!-- Hiển thị avatar nhóm bên trái -->
            <div class="col-md-4 d-flex align-items-center">
                <img src="{{ asset('storage/' . ($group->avatar ?? 'groups/avatars/group_icon.png')) }}" alt="Avatar của nhóm {{ $group->name }}" class="rounded-circle thumbnail" style="width: 80px; height: 80px; margin-right: 15px;">
                <h1 class="h4">{{ $group->name }}</h1>
            </div>

            <!-- Nút chỉnh sửa và xóa nhóm (dành cho người tạo) -->
            @if ($group->creator_id === Auth::id())
            <div class="row mt-3">
                <div class="col-md-6 text-right">
                    <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-warning btn-sm">Chỉnh sửa nhóm</a>
                </div>
                <div class="col-md-6 text-left">
                    <form action="{{ route('groups.destroy', $group->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')" class="btn btn-danger btn-sm">Xóa nhóm</button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="group-info mt-4">
            <p><strong>Số lượng thành viên:</strong> {{ $group->members->count() }}</p>
            <p><strong>Nội dung:</strong> {{ $group->description }}</p>
            <p><strong>Người tạo:</strong> {{ $group->creator->username ?? 'Không rõ' }}</p>
            <p><strong>Ngày tạo:</strong> {{ $group->created_at->format('d/m/Y H:i') }}</p>

            <!-- Thêm trạng thái nhóm -->
            <p><strong>Trạng thái nhóm:</strong>
                @if($group->requires_approval)
                Cần phê duyệt tham gia
                @else
                Mở (Không cần phê duyệt tham gia)
                @endif
            </p>
        </div>

        @php
        $isMember = $group->members()->where('user_id', Auth::id())->exists();
        $hasRequested = $group->memberRequests()->where('user_id', Auth::id())->exists();
        @endphp

        @if(Auth::id() !== $group->creator_id) <!-- Kiểm tra nếu người dùng không phải là người tạo -->
        @if($group->requires_approval)
        @if(!$isMember && !$hasRequested)
        <form action="{{ route('groups.join', $group->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary mt-3">Yêu Cầu Tham Gia Nhóm</button>
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
            <button type="submit" class="btn btn-primary mt-3">Tham Gia Nhóm</button>
        </form>
        @else
        <form action="{{ route('groups.leave', $group->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-warning mt-3">Rời Nhóm</button>
        </form>
        <p>Bạn đã là thành viên của nhóm này.</p>
        @endif
        @endif
        @else
        <p>Xin chào chủ nhóm!</p>
        @endif

        <!-- Hiển thị danh sách thành viên -->
        <h3 class="mt-4">Thành viên trong nhóm:</h3>
        <div class="row">
            @foreach ($group->members as $user)
            <div class="col-6 col-md-4 col-lg-3 mb-3 text-center">
                <a href="{{ route('users.profile.index', $user->id) }}">
                    <img src="{{ $user->profile_picture ? (filter_var($user->profile_picture, FILTER_VALIDATE_URL) ? $user->profile_picture : asset('storage/' . $user->profile_picture)) : asset('storage/images/avataricon.png') }}"
                        alt="Avatar" class="rounded-circle" height="50" width="50" loading="lazy">
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
        <h3 class="mt-4">Các yêu cầu tham gia:</h3>
        <ul class="list-unstyled overflow-auto" style="max-height: 200px;">
            @foreach ($group->joinRequests()->where('status', 'pending')->get() as $request)
            <li>
                {{ $request->user->username }}
                <form action="{{ route('groups.approve', [$group->id, $request->user_id]) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">Duyệt</button>
                </form>
                <form action="{{ route('groups.reject', [$group->id, $request->user_id]) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Từ chối</button>
                </form>
            </li>
            @endforeach
        </ul>
        @endif

        <!-- Hiển thị bài viết trong nhóm (nếu có) -->
        @if($group->posts->isNotEmpty())
        @php
        // Lọc các bài viết có thể hiển thị
        $visiblePosts = $group->posts->filter(function($post) {
        return $post->group->visibility === 'public' ||
        (Auth::check() && $post->group->members->contains(Auth::id()));
        });
        @endphp

        @if ($visiblePosts->isNotEmpty())
        <h3 class="mt-4">Bài viết trong nhóm:</h3>
        <ul class="list-unstyled">
            @foreach ($visiblePosts as $post)
            <li>{{ $post->title }}</li>
            @endforeach
        </ul>
        @else
        <p>Tất cả bài viết trong nhóm này đều là bài viết riêng tư.</p>
        @endif
        @else
        <p>Nhóm này chưa đăng tải bài viết nào.</p>
        @endif
    </div>

    @else
    <div class="right-sidebars p-3">
        <h3 class="sidebar-title">Gợi ý theo dõi</h3>
        <ul class="suggested-users-list list-unstyled">
            @forelse ($usersToFollow as $user)
            <li class="d-flex align-items-center mb-3 follow-item">
                <a href="{{ route('users.profile.index', ['user' => $user->id]) }}">
                    <img
                        src="{{ filter_var($user->profile_picture, FILTER_VALIDATE_URL) ? $user->profile_picture : asset('storage/' . ($user->profile_picture ?? 'images/avataricon.png')) }}"
                        alt="Profile picture of {{ $user->username }}"
                        class="rounded-circle"
                        height="40"
                        width="40"
                        loading="lazy" />
                </a>
                <div class="info ms-2">
                    <h5 class="mb-0">{{ $user->username }}</h5>
                    <p class="mb-0 text-muted">{{ $user->role }}</p>
                </div>
                <div class="ms-auto">
                    @php
                    $currentUserId = Auth::id();
                    $friendship = \App\Models\Friendship::where(function ($query) use ($currentUserId, $user) {
                    $query->where('sender_id', $currentUserId)
                    ->where('receiver_id', $user->id);
                    })
                    ->orWhere(function ($query) use ($currentUserId, $user) {
                    $query->where('sender_id', $user->id)
                    ->where('receiver_id', $currentUserId);
                    })
                    ->first();
                    @endphp

                    @if (!$friendship)
                    <!-- Nút gửi yêu cầu kết bạn -->
                    <form action="{{ route('friend.sendRequest', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Thêm bạn</button>
                    </form>
                    @elseif ($friendship->status === 'pending' && $friendship->sender_id === Auth::id())
                    <!-- Đã gửi yêu cầu kết bạn -->
                    <p class="text-muted">Đã gửi yêu cầu</p>
                    <form action="{{ route('friend.cancelRequest', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Hủy yêu cầu</button>
                    </form>

                    @elseif ($friendship->status === 'pending' && $friendship->receiver_id === $currentUserId)
                    <!-- Nút chấp nhận yêu cầu kết bạn -->
                    <form action="{{ route('friend.acceptRequest', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">Chấp nhận</button>
                    </form>
                    <form action="{{ route('friend.declineRequest', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">Từ chối</button>
                    </form>
                    @elseif ($friendship->status === 'accepted')
                    <!-- Đã là bạn bè -->
                    <p class="text-muted">Bạn bè</p>
                    @elseif ($friendship->status === 'declined')
                    <!-- Đã từ chối yêu cầu -->
                    <p class="text-muted">Yêu cầu đã bị từ chối</p>
                    <form action="{{ route('friend.sendRequest', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Gửi lại yêu cầu</button>
                    </form>
                    @elseif ($friendship->status === 'blocked')
                    <!-- Đã bị chặn -->
                    <p class="text-muted">Bạn đã bị chặn</p>
                    @endif

                </div>
            </li>
            @empty
            <p class="text-center">Không có người dùng nào để theo dõi.</p>
            @endforelse
        </ul>
    </div>
    @endif
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
                <div class="comment p-3 mb-3 border rounded shadow-sm" id="comment-{{ $comment->id }}" style="background-color: #f9f9f9;">
                    <div class="d-flex align-items-center mb-2">
                        <img src="{{ $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : asset('storage/images/avataricon.png') }}" alt="Avatar" class="rounded-circle me-3" width="40" height="40" loading="lazy" style="border: 1px solid #ddd;">
                        <div>
                            <strong>{{ $comment->user->username }}</strong>
                            <small class="text-muted"> • {{ $comment->created_at->isoFormat('DD/MM/YYYY HH:mm') }} ({{ $comment->created_at->diffForHumans() }})</small>
                        </div>
                    </div>
                    <div class="ms-4">
                        <p class="mb-2" style="font-size: 1rem; line-height: 1.5;">{{ $comment->content }}</p>
                        @if($comment->image_url)
                        <div class="comment-image mb-2">
                            <img src="{{ asset('storage/' . $comment->image_url) }}" class="img-fluid rounded" alt="Comment Image" loading="lazy" style="max-width: 100%; height: auto;">
                        </div>
                        @endif
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex">
                                <button class="btn btn-sm btn-outline-secondary like-button me-2" data-comment-id="{{ $comment->id }}" style="border-radius: 20px; padding: 5px 10px;">
                                    <i class="far fa-thumbs-up"></i> <span class="like-count">{{ $comment->likes_count }}</span>
                                </button>
                                <button class="btn btn-sm btn-outline-primary reply-button" data-comment-id="{{ $comment->id }}" style="border-radius: 20px; padding: 5px 10px;">
                                    <i class="fas fa-reply"></i> Trả lời
                                </button>
                            </div>
                            <!-- Nút xóa nếu cần -->
                            @if(auth()->check() && auth()->user()->id == $comment->user_id) <!-- Kiểm tra quyền xóa -->
                            <button class="btn btn-sm btn-outline-danger delete-button" data-comment-id="{{ $comment->id }}" style="border-radius: 20px; padding: 5px 10px;">
                                <i class="fas fa-trash-alt"></i> Xóa
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="replies ms-4 mt-3" id="replies-{{ $comment->id }}"></div> <!-- Khu vực để hiển thị các bình luận trả lời -->
                </div>
                @endforeach
                @else
                <p>Bạn cần đăng nhập để xem được bình luận.</p>
                @endif
            </div>
        </div>
        @if(auth()->check() && isset($post) && $post->id)
        <form id="commentForm" data-post-id="{{ $post->id }}" action="{{ route('comments.store', $post->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: auto;">
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

@if(isset($post) && $post->id)
<!-- Form ẩn để gửi báo cáo -->
<form id="reportForm-{{ $post->id }}" action="{{ route('admin.reports.store') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="post_id" value="{{ $post->id }}">
    <input type="hidden" name="reason" id="reasonInput-{{ $post->id }}" value="">
</form>

@endif

@endsection