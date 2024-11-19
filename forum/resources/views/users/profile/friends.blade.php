@extends('layouts.users')
@section('title', 'Friends')

@section('content')
<style>
    .mx-6 {
        margin-left: 5rem;
        margin-right: 5rem;
    }

    .cover-image {
        width: 100%;
        height: 280px;
        /* Chiều cao cố định */
        background-position: center center;
        background-size: cover;
        position: relative;
    }

    .cover-image img {
        width: 100%;
        height: 100%;
    }

    /* Ảnh đại diện */
    .profile-pic {
        width: 120px;
        height: 120px;
        transform: translate(0%, -50%);
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .profile-pic img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-details {
        color: #333;
    }

    .profile-details h1 {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
    }

    .profile-details p {
        color: #777;
        font-size: 16px;
    }

    .profile-nav {
        display: flex;
        justify-content: center;
        gap: 15px;
        padding: 10px 0;
        border-bottom: 2px solid #007bff;
        margin-bottom: 20px;
    }

    .profile-nav a {
        color: #007bff;
        font-weight: bold;
        text-decoration: none;
        padding: 10px;
    }

    .profile-nav a:hover {
        text-decoration: underline;
    }

    .content {
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
    }

    .mb-3,
    .content .form-label {
        margin-bottom: 10px;
    }

    h5 {
        color: #333;
        font-weight: bold;
        margin-top: 20px;
    }

    ul.list-group {
        padding-left: 0;
        margin-top: 10px;
    }

    .list-group-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .friend-list {
        display: flex;
        flex-wrap: wrap;
    }

    .friend-card {
        width: 120px;
        /* Chiều rộng của thẻ bạn bè */
        border: 1px solid #ccc;
        /* Viền */
        border-radius: 8px;
        /* Bo góc */
        margin: 10px;
        /* Khoảng cách giữa các thẻ */
        text-align: center;
        /* Căn giữa nội dung */
        padding: 10px;
        /* Padding bên trong */
        background-color: #f9f9f9;
        /* Màu nền */
    }

    .friend-avatar {
        width: 80px;
        /* Chiều rộng ảnh */
        height: 80px;
        /* Chiều cao ảnh */
        border-radius: 50%;
        /* Bo tròn ảnh */
    }

    .friend-name {
        display: block;
        /* Đảm bảo tên người bạn nằm trên một dòng mới */
        margin-top: 5px;
        /* Khoảng cách phía trên tên */
        font-weight: bold;
        /* Đậm */
    }

    .friend-status {
        display: block;
        /* Đảm bảo trạng thái nằm trên một dòng mới */
        margin-top: 5px;
        /* Khoảng cách phía trên trạng thái */
        color: #555;
        /* Màu sắc cho trạng thái */
    }
</style>
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-11 col-sm-12 col-12 offset-lg-2 content-cols" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">

            <!-- Profile -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-12" style=" background-color: #fff">
                <div class="cover-image position-relative">
                    <img src="{{ $user->cover_image ? asset('storage/' . $user->cover_image) : asset('storage/images/covers/1200x300.png') }}" alt="Avatar" style="max-width:100%" class="rounded thumbnail">
                </div>

                <!-- Ảnh đại diện và thông tin người dùng -->
                <div class="profile-wrapper d-flex flex-column align-items-center">
                    <!-- Ảnh đại diện -->
                    <div class="profile-pic">
                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('storage/images/avataricon.png') }}" alt="Avatar" class="rounded thumbnail">
                    </div>

                    <!-- Thông tin người dùng -->
                    <div class="profile-details text-center mt-3">
                        <h1>{{ $user->username ?? 'Tên người dùng' }}</h1>
                        <p class="text-muted">{{ $user->role ?? 'Vai trò' }} | {{ $user->status ?? 'Trạng thái' }}</p>
                    </div>
                </div>

                <div class="profile-nav">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ route('users.profile.index', ['user' => $user->id]) }}">Frofile</a>
                    <a href="{{ route('users.profile.friend', ['user' => $user->id, 'section' => 'friends']) }}">Friends</a>
                    <a href="{{ route('users.groups.index') }}">Groups</a>

                    <!-- Kiểm tra nếu người dùng là chủ nhóm hoặc thành viên trong ít nhất một nhóm -->
                    @if (isset($groups) && $groups->isNotEmpty())
                    @php
                    $firstGroup = $groups->first();
                    $isGroupOwnerOrMember = $groups->contains(function($group) {
                    return $group->isOwner(Auth::user()) || $group->isMember(Auth::user());
                    });
                    @endphp

                    <!-- Nếu là chủ nhóm hoặc thành viên của ít nhất một nhóm -->
                    @if ($isGroupOwnerOrMember)
                    <a href="{{ route('groups.chat', $firstGroup->id) }}">Chat</a>
                    @endif
                    @endif

                    <a href="{{ route('forums.index') }}">Forums</a>
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Tùy Chọn
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('users.posts.published') }}">Bài Viết Đã Xuất Bản</a></li>

                        <!-- Kiểm tra nếu có thư mục -->
                        @if(isset($folders) && $folders->isNotEmpty())
                        <li><a class="dropdown-item" href="#">Không có bài viết đã lưu</a></li>
                        @else
                        <!-- Liên kết đến trang chọn thư mục -->
                        <li><a class="dropdown-item" href="{{ route('users.posts.savePost') }}">Thư Mục Yêu thích</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Content Section -->
                <div class="content">
                    <!-- Success Message -->
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    <!-- User Information -->
                    <div class="row">
                        <!-- Khung chứa tất cả ảnh của người dùng -->
                        <div class="col-md-5 mx-auto mt-5">
                            <h1 class="text-center">Tất cả ảnh của bạn</h1>

                            @php
                            $hasImages = $user->profile_picture || $user->cover_image || $user->posts->where('status', 'published')->whereNotNull('image_url')->count() > 0;
                            @endphp

                            @if (!$hasImages)
                            <p class="text-center">Người dùng chưa có hình ảnh đã đăng.</p>
                            @else
                            <div class="d-flex flex-wrap gap-2 justify-content-center" style="overflow-y: auto; max-height: 400px;">
                                @php
                                $images = [];

                                // Lấy ảnh đại diện nếu có
                                if ($user->profile_picture) {
                                $images[] = asset('storage/' . $user->profile_picture);
                                }

                                // Lấy ảnh bìa nếu có
                                if ($user->cover_image) {
                                $images[] = asset('storage/' . $user->cover_image);
                                }

                                // Lấy ảnh từ các bài đăng (bao gồm ảnh từ bảng post_images)
                                foreach ($user->posts as $post) {
                                // Lấy ảnh chính của bài đăng nếu có
                                if ($post->image_url && ($post->status === 'published' || Auth::id() === $user->id)) {
                                $fileExtension = strtolower(pathinfo($post->image_url, PATHINFO_EXTENSION));

                                // Kiểm tra định dạng và phân loại vào mảng ảnh hoặc video
                                if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $images[] = asset('storage/' . $post->image_url);
                                } elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg'])) {
                                $videos[] = asset('storage/public/' . $post->image_url);
                                }
                                }

                                // Lấy ảnh phụ từ bảng post_images nếu có
                                if ($post->postImages) {
                                foreach ($post->postImages as $image) {
                                $images[] = asset('storage/' . $image->file_path);
                                }
                                }
                                }
                                @endphp

                                <!-- Hiển thị tất cả ảnh -->
                                <div class="d-flex flex-wrap gap-2 justify-content-center" style="overflow-y: auto; max-height: 400px;">
                                    @foreach ($images as $image)
                                    <div class="thumbnail-wrapper" style="width: 100px; height: 100px; position: relative;">
                                        <img src="{{ $image }}" alt="Ảnh" class="rounded thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    @endforeach
                                    @foreach ($videos as $video)
                                    <div class="video-wrapper" style="width: 200px; height: 150px; position: relative;">
                                        <video controls style="width: 100%; height: 100%;">
                                            <source src="{{ $video }}" type="video/{{ pathinfo($video, PATHINFO_EXTENSION) }}">
                                            Trình duyệt của bạn không hỗ trợ video.
                                        </video>
                                    </div>
                                    @endforeach
                                </div>

                            </div>
                            @endif
                        </div>

                        <!-- Thông tin bạn bè của người dùng -->
                        <div class="col-md-3 mx-auto mt-4">
                            <h5>Bạn bè</h5>
                            <!-- Hiển thị danh sách bạn bè -->
                            @if ($friends->isNotEmpty())
                            <div class="friend-list">
                                @foreach ($friends as $friend)
                                <div class="friend-card">
                                    <a href="{{ route('users.profile.index', ['user' => $friend->id]) }}">
                                        <img src="{{ $friend->profile_picture ? asset('storage/' . $friend->profile_picture) : asset('storage/images/avataricon.png') }}" alt="Avatar" class="friend-avatar" loading="lazy" style="width: 50px; height: 50px;">
                                    </a>
                                    <span class="friend-name">{{ $friend->username }}</span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p>Không có bạn bè nào.</p>
                            @endif
                        </div>

                        <!-- Bài viết yêu thích -->
                        <div class="col-md-3 mx-auto mt-5">
                            @if (!$isOwnProfile)
                            @php
                            // Kiểm tra các yêu cầu kết bạn đã gửi và nhận
                            $friendship = Auth::user()->sentFriendRequests->where('receiver_id', $user->id)->first();
                            $friendshipReverse = Auth::user()->receivedFriendRequests->where('sender_id', $user->id)->first();
                            @endphp

                            @if (is_null($friendship) && is_null($friendshipReverse))
                            <!-- Nút gửi yêu cầu kết bạn -->
                            <form action="{{ route('friend.sendRequest', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit">Gửi yêu cầu kết bạn</button>
                            </form>
                            @elseif ($friendship && $friendship->status === 'pending')
                            <p>Đã gửi yêu cầu kết bạn. Đang chờ phản hồi.</p>
                            @elseif ($friendship && $friendship->status === 'accepted')
                            <p>Đã là bạn bè với {{ $user->name }}.</p>
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}'s Avatar" style="width: 50px; height: 50px;">
                            @elseif ($friendshipReverse)
                            @if ($friendshipReverse->status === 'pending')
                            <p>{{ $user->name }} đã gửi yêu cầu kết bạn cho bạn.</p>
                            <form action="{{ route('friend.acceptRequest', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit">Chấp nhận</button>
                            </form>
                            @endif
                            @endif
                            @endif

                            <!-- Hiển thị các yêu cầu kết bạn đã nhận -->
                            @if ($receivedFriendRequests->isNotEmpty())
                            <h3>Các yêu cầu kết bạn:</h3>
                            @foreach ($receivedFriendRequests as $request)
                            <p>
                                {{ $request->sender->username }} đã gửi cho bạn một yêu cầu kết bạn.
                                @if ($request->status === 'pending') <!-- Kiểm tra xem yêu cầu đang ở trạng thái chờ xử lý -->
                            <form action="{{ route('friend.acceptRequest', $request->sender_id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit">Chấp nhận</button>
                            </form>
                            <form action="{{ route('friend.declineRequest', $request->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit">Từ chối</button>
                            </form>
                            @elseif ($request->status === 'accepted')
                            <span>Yêu cầu đã được chấp nhận.</span> <!-- Hiển thị thông báo nếu yêu cầu đã được chấp nhận -->
                            @elseif ($request->status === 'declined')
                            <span>Yêu cầu đã bị từ chối.</span> <!-- Hiển thị thông báo nếu yêu cầu đã bị từ chối -->
                            @endif
                            </p>
                            @endforeach
                            @else
                            <p>Không có yêu cầu kết bạn nào.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection