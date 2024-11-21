@extends('layouts.users')

@section('title', 'Thông Báo')

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
                <div class="user-info text-center mb-4" style="background-color: black; background-image: linear-gradient(135deg, #52545f 0%, #383a45 50%);">
                    @if(Auth::check())
                    <a class="dropdown-item" href="{{ route('users.profile.index', Auth::user()->id) }}">
                        <!-- Kiểm tra nếu profile_picture là URL hợp lệ, nếu không thì lấy ảnh trong storage -->
                        <img src="{{ 
                    (filter_var(auth()->user()->profile_picture, FILTER_VALIDATE_URL)) 
                    ? auth()->user()->profile_picture 
                    : (auth()->user()->profile_picture 
                        ? asset('storage/' . auth()->user()->profile_picture) 
                        : asset('storage/images/avataricon.png')) 
                }}"
                            alt="Profile picture of {{ auth()->user()->username }}"
                            class="rounded-circle" style="width: 45px; height: 50px;">
                    </a>
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

        <!-- Phần nội dung -->
        <div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color: #fff; padding: 20px; border-radius: 10px;">
            <div class="post-container">
                <h1 class="text-center mb-4">📢 Thông báo của bạn</h1>

                <!-- Nút đánh dấu tất cả là đã đọc -->
                <div class="text-end mb-4">
                    <a href="{{ route('notifications.markAllAsRead') }}" class="btn btn-primary">
                        <i class="fas fa-check-double"></i> Đánh dấu tất cả là đã đọc
                    </a>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs" id="notificationTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#unread">Chưa đọc</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#read">Đã đọc</a>
                    </li>
                </ul>

                <div class="tab-content mt-4">
                    <!-- Tab thông báo chưa đọc -->
                    <div id="unread" class="tab-pane fade show active">
                        @if($unreadNotifications->isEmpty())
                        <div class="alert alert-info text-center">Không có thông báo chưa đọc.</div>
                        @else
                        @foreach($unreadNotifications as $notification)
                        <div class="alert alert-warning mb-4">
                            <strong>{{ $notification->data['title'] ?? 'Thông báo' }}</strong>
                            <p>{{ $notification->data['message'] }}</p>

                            <!-- Kiểm tra yêu cầu kết bạn -->
                            @if(isset($notification->data['friendship_request']))
                            <div class="notification-item">
                                <p><strong>{{ $notification->data['friendship_request']['message'] }}</strong></p>
                                <a href="{{ route('friendship.accept', $notification->data['friendship_request']['sender_id']) }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-check"></i> Chấp nhận
                                </a>
                                <a href="{{ route('friendship.decline', $notification->data['friendship_request']['sender_id']) }}" class="btn btn-danger btn-sm">
                                    <i class="bi bi-x"></i> Từ chối
                                </a>
                            </div>

                            <!-- Kiểm tra thông báo bài viết -->
                            @elseif(isset($notification->data['post_id']))
                            <a href="{{ route('users.index', $notification->data['post_id']) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Xem bài viết
                            </a>

                            <!-- Kiểm tra thông báo nhóm -->
                            @elseif(isset($notification->data['group_id']))
                            <a href="{{ route('users.groups.chat', $notification->data['group_id']) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-users"></i> Xem nhóm
                            </a>

                            <!-- Kiểm tra thông báo sản phẩm -->
                            @elseif(isset($notification->data['product_id']))
                            <a href="{{ route('chat.product', ['productId' => $notification->data['product_id'], 'receiverId' => $notification->data['sender_id']]) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-chat-dots"></i> Tin nhắn về sản phẩm
                            </a>
                            <p>{{ $notification->data['message_content'] }}</p>

                            <!-- Kiểm tra thông báo tin nhắn cá nhân -->
                            @elseif(isset($notification->data['receiver_id']))
                            <a href="{{ route('chat.show', ['chat_id' => $notification->data['chat_id'], 'receiver_id' => $notification->data['receiver_id']]) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-chat-dots"></i> Xem tin nhắn cá nhân
                            </a>
                            <p>{{ $notification->data['message_content'] }}</p>
                            @endif

                            <!-- Nút đánh dấu thông báo là đã đọc -->
                            <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline-block ms-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Đánh dấu là đã đọc
                                </button>
                            </form>
                        </div>
                        @endforeach
                        @endif
                    </div>

                    <!-- Tab thông báo đã đọc -->
                    <div id="read" class="tab-pane fade">
                        @if($readNotifications->isEmpty())
                        <div class="alert alert-info text-center">Không có thông báo đã đọc.</div>
                        @else
                        @foreach($readNotifications as $notification)
                        <div class="alert alert-secondary mb-4">
                            <strong>{{ $notification->data['title'] ?? 'Thông báo' }}</strong>
                            <p>{{ $notification->data['message'] }}</p>

                            <!-- Kiểm tra thông báo kết bạn -->
                            @if(isset($notification->data['friendship_request']))
                            <p><strong>{{ $notification->data['friendship_request']['message'] }}</strong></p>
                            <a href="{{ route('friendship.accept', $notification->data['friendship_request']['sender_id']) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-check"></i> Chấp nhận
                            </a>
                            <a href="{{ route('friendship.decline', $notification->data['friendship_request']['sender_id']) }}" class="btn btn-danger btn-sm">
                                <i class="bi bi-x"></i> Từ chối
                            </a>
                            @elseif(isset($notification->data['post_id']))
                            <!-- Xử lý thông báo bài viết -->
                            <a href="{{ route('users.index', $notification->data['post_id']) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Xem bài viết
                            </a>
                            @elseif(isset($notification->data['group_id']))
                            <!-- Xử lý thông báo nhóm -->
                            <a href="{{ route('users.groups.chat', $notification->data['group_id']) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-users"></i> Xem nhóm
                            </a>
                            @elseif(isset($notification->data['product_id']))
                            <!-- Xử lý thông báo sản phẩm -->
                            <a href="{{ route('chat.product', ['productId' => $notification->data['product_id'], 'receiverId' => $notification->data['sender_id']]) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-chat-dots"></i> Tin nhắn về sản phẩm
                            </a>
                            <p>{{ $notification->data['message_content'] }}</p>
                            @elseif(isset($notification->data['receiver_id']))
                            <!-- Xử lý thông báo tin nhắn cá nhân -->
                            <a href="{{ route('chat.show', ['chat_id' => $notification->data['chat_id'], 'receiver_id' => $notification->data['receiver_id']]) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-chat-dots"></i> Xem tin nhắn cá nhân
                            </a>
                            <p>{{ $notification->data['message_content'] }}</p>
                            @endif

                            <small class="text-muted d-block mt-2">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        @endforeach

                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $readNotifications->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection