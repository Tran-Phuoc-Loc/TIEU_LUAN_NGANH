@extends('layouts.users')
@section('title', 'Thông tin người dùng')

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
</style>
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-11 col-sm-12 col-12 offset-lg-2 content-cols" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">

            <!-- Profile -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-12" style=" background-color: #fff">
                <div class="cover-image position-relative">
                    <img src="{{ $user->cover_image ? asset('storage/' . $user->cover_image) : asset('storage/images/covers/1200x300.png') }}" alt="Avatar" style="max-width:100%">
                </div>

                <!-- Ảnh đại diện và thông tin người dùng -->
                <div class="profile-wrapper d-flex flex-column align-items-center">
                    <!-- Ảnh đại diện -->
                    <div class="profile-pic">
                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('storage/images/avataricon.png') }}" alt="Avatar">
                    </div>

                    <!-- Thông tin người dùng -->
                    <div class="profile-details text-center mt-3">
                        <h1>{{ $user->username ?? 'Tên người dùng' }}</h1>
                        <p class="text-muted">{{ $user->role ?? 'Vai trò' }} | {{ $user->status ?? 'Trạng thái' }}</p>
                    </div>
                </div>

                <div class="profile-nav">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ route('users.profile.friend', ['user' => $user->id, 'section' => 'friends']) }}">Friends</a>
                    <a href="{{ route('users.groups.index') }}">Groups</a>

                    <!-- Kiểm tra nếu người dùng là chủ nhóm hoặc thành viên trong ít nhất một nhóm -->
                    @if ($groups->isNotEmpty())
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
                        <div class="col-md-3 mx-auto mt-5">
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

                        <!-- Thông tin cá nhân của người dùng -->
                        <div class="col-md-6 mx-auto mt-4">
                            <h5>Thông tin cá nhân</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Tên:</th>
                                        <td>{{ $user->username ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $user->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Số lượng bài viết đã xuất bản:</th>
                                        <td>{{ $publishedCount ?? 0 }}</td>
                                    </tr>
                                    @if(Auth::id() === $user->id)
                                    <tr>
                                        <th>Số lượng bài viết dạng draft:</th>
                                        <td>{{ $draftCount ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <a href="{{ route('users.posts.drafts') }}" class="btn btn-success">Những bài viết dạng draft</a>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Ngày tham gia:</th>
                                        <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Trạng thái tài khoản:</th>
                                        <td>{{ ucfirst($user->status ?? 'N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Số lượng bài viết:</th>
                                        <td>{{ $user->post_count ?? 0 }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            @if(Auth::check() && Auth::id() === $user->id)
                            <div class="mt-4">
                                <a href="{{ route('users.profile.edit', $user->id) }}" class="btn btn-primary">Chỉnh Sửa Thông Tin</a>
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">Quay lại</a>
                            </div>
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

                                @if (Auth::id() !== $user->id) <!-- Kiểm tra nếu người dùng không phải là chính họ -->
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
                                    @endif
                                @else
                                    <p>Bạn đang xem hồ sơ của chính mình, không thể gửi yêu cầu kết bạn với chính mình.</p>
                                @endif
                            @endif

                            <!-- Hiển thị các yêu cầu kết bạn đã nhận, chỉ khi người dùng đang xem hồ sơ của chính mình -->
                            @if ($isOwnProfile && $receivedFriendRequests->isNotEmpty())
                                <h3>Các yêu cầu kết bạn:</h3>
                                @foreach ($receivedFriendRequests as $request)
                                    @if ($request->receiver_id === Auth::id()) <!-- Kiểm tra xem người dùng hiện tại có phải là người nhận yêu cầu -->
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
                                                <span>Yêu cầu đã được chấp nhận.</span>
                                            @elseif ($request->status === 'declined')
                                                <span>Yêu cầu đã bị từ chối.</span>
                                            @endif
                                        </p>
                                    @endif
                                @endforeach
                            @else
                                @if ($isOwnProfile)
                                    <!-- Khi người dùng đang xem hồ sơ của chính họ mà không có yêu cầu kết bạn nào -->
                                    <p>Bạn hiện không có yêu cầu kết bạn nào.</p>
                                @else
                                    <!-- Khi người dùng đang xem hồ sơ của người khác -->
                                    <p>Hồ sơ này không có yêu cầu kết bạn nào liên quan đến bạn.</p>
                                @endif
                            @endif

                            <!-- Hiển thị danh sách bạn bè -->
                            @if ($friends->isNotEmpty())
                                <h3>Danh sách bạn bè:</h3>
                                <ul>
                                    @foreach ($friends as $friend)
                                        <li>{{ $friend->username }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>Không có bạn bè nào.</p>
                            @endif

                            @if(Auth::check() && Auth::id() === $user->id)
                            <div class="mb-3">
                                <h5>Bài viết yêu thích</h5>
                                @if($favoritePosts->isEmpty())
                                <p>Chưa có bài viết yêu thích.</p>
                                @else
                                <ul class="list-group">
                                    @foreach($favoritePosts as $post)
                                    <li class="list-group-item">
                                        <a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a>
                                        <span class="badge bg-primary float-end">{{ $post->likes_count }} lượt thích</span>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection