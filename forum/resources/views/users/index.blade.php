@extends('layouts.users')

@section('title', 'Danh sách bài viết')
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
@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Menu điều hướng cho màn hình lớn -->
        <div class="col-lg-2 col-md-1 sidebar d-none d-md-block" style="background-color: #fff; position: fixed; height: 100vh; overflow-y: auto;">
            <div class="vertical-navbar">
                <!-- Thông tin người dùng -->
                <div class="user-info text-center mb-4" style="background-color: black;background-image: linear-gradient(135deg, #52545f 0%, #383a45 50%);">
                    @if(auth()->check())
                    <a class="dropdown-item" href="{{ route('users.profile.index', Auth::user()->id) }}"><img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('storage/images/avataricon.png') }}"
                            alt="Profile picture of {{ auth()->user()->username }}"
                            class="rounded-circle" style="width: 45px; height: 50px;"></a>
                    <h5 class="d-none d-md-block" style="color: #fff;">{{ auth()->user()->username }}</h5>
                    <hr style="border-top: 1px solid black; margin: 10px 0;">
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
                        @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index', ['user_posts' => 'true']) }}">
                                <i class="bi bi-pencil"></i>
                                <span class="d-none d-lg-inline">Bài viết của bạn</span>
                            </a>
                        </li>
                        @endauth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">
                                <i class="bi bi-folder"></i>
                                <span class="d-none d-lg-inline">Danh mục</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forums.index') }}">
                                <i class="bi bi-wechat"></i>
                                <span class="d-none d-lg-inline">Diễn đàn</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.groups.index') }}">
                                <i class="bi bi-people"></i>
                                <span class="d-none d-lg-inline">Nhóm tham gia</span>
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
                            @if (isset($groups) && $groups->isNotEmpty())
                            @php $firstGroup = $groups->first(); @endphp
                            <a href="{{ route('groups.chat', $firstGroup->id) }}">
                                <i class="fas fa-comment-sms" style="font-size: 40px"></i>
                            </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Phần nội dung bài viết -->
        <div class="col-lg-7 col-md-7 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
            <div class="post-container">
                <!-- Thêm phần lọc -->
                <div class="filter-buttons my-3 d-flex justify-content-end gap-2">
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
                                <img src="{{ $post->user->profile_picture ? asset('storage/' . $post->user->profile_picture) : asset('storage/images/avataricon.png') }}" alt="Avatar" class="post-avatar" loading="lazy">
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

                <!-- Sidebar phải: Gợi ý người theo dõi -->
                <div class="col-lg-3 col-md-3 mt-lg-0 right-sidebar" style="background-color: #fff; position: fixed; right: 0; bottom:0; height: calc(100vh - 106px); overflow-y: auto;">

                    @if($group)
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
                                <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-warning btn-sm mr-2">Chỉnh sửa nhóm</a>

                                <!-- Nút xóa nhóm -->
                                <form action="{{ route('groups.destroy', $group->id) }}" method="POST" style="display:inline;" class="ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')" class="btn btn-danger btn-sm mr-2 ">Xóa nhóm</button>
                                </form>
                            </div>

                            @endif
                            <p>Số lượng thành viên: {{ $group->members->count() }}</p>
                            <p><strong>Nội Dung:</strong> {{ $group->description }}</p>
                            <p><strong>Người tạo:</strong> {{ $group->creator->username ?? 'Không rõ' }}</p>
                            <p><strong>Ngày tạo:</strong> {{ $group->created_at->format('d/m/Y H:i') }}</p>

                            <!-- Thêm trạng thái nhóm -->
                            @if($group->requires_approval)
                            <p>Trạng thái nhóm: Cần phê duyệt tham gia</p>
                            @else
                            <p>Trạng thái nhóm: Mở (Không cần phê duyệt tham gia)</p>
                            @endif

                            @php
                            $isMember = $group->members()->where('user_id', Auth::id())->exists();
                            $hasRequested = $group->memberRequests()->where('user_id', Auth::id())->exists();
                            @endphp

                            @if(Auth::id() !== $group->creator_id) <!-- Kiểm tra nếu người dùng không phải là người tạo -->
                            @if($group->requires_approval)
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
                                    <a href="{{ route('users.profile.index', $user->id) }}">
                                        <!-- Avatar người dùng -->
                                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('storage/images/avataricon.png') }}"
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
                    @else
                    <div class="right-sidebars p-3">
                        <h3 class="sidebar-title">Gợi ý theo dõi</h3>
                        <ul class="suggested-users-list list-unstyled">
                            @forelse ($usersToFollow as $user)
                            <li class="d-flex align-items-center mb-3 follow-item">
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