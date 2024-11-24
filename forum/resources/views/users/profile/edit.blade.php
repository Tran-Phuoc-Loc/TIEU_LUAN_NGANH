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
                    <img src="{{ $user->cover_image ? asset('storage/' . $user->cover_image) : asset('storage/images/covers/1200x300.png') }}" alt="Avatar" style="max-width:100%; object-fit:cover;" class="rounded thumbnail" loading="lazy">
                </div>

                <!-- Ảnh đại diện và thông tin người dùng -->
                <div class="profile-wrapper d-flex flex-column align-items-center">
                    <!-- Ảnh đại diện -->
                    <div class="profile-pic">
                        <img src="{{ 
                                (filter_var($user->profile_picture, FILTER_VALIDATE_URL)) 
                                ? $user->profile_picture 
                                : ($user->profile_picture 
                                    ? asset('storage/' . $user->profile_picture) 
                                    : asset('storage/images/avataricon.png')) 
                            }}"
                            alt="Avatar" class="rounded thumbnail" loading="lazy">
                    </div>

                    <!-- Thông tin người dùng -->
                    <div class="profile-details text-center mt-3">
                        <h1>{{ $user->username ?? 'Tên người dùng' }}</h1>
                        <p class="text-muted">{{ $user->role ?? 'Vai trò' }} | {{ $user->status ?? 'Trạng thái' }}</p>
                    </div>
                </div>

                <div class="profile-nav">
                    <a href="{{ url('/') }}">Trang chủ</a>
                    <a href="{{ route('users.profile.index', ['user' => $user->id]) }}">Hồ sơ</a>
                    <a href="{{ route('users.profile.friend', ['user' => $user->id, 'section' => 'friends']) }}">Bạn bè</a>
                    <a href="{{ route('users.groups.index') }}">Nhóm</a>

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
                    <a href="{{ route('groups.chat', $firstGroup->id) }}">Tin nhắn</a>
                    @endif
                    @endif

                    <a href="{{ route('forums.index') }}">Diễn đàn</a>
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Tùy Chọn
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('users.posts.published') }}">Bài Viết Đã Xuất Bản</a></li>
                        <li><a class="dropdown-item" href="{{ route('users.liked.posts') }}">Bài Viết Đã Thích</a></li>
                        <!-- Kiểm tra nếu có thư mục -->
                        @if($folders->isEmpty())
                        <li><a class="dropdown-item" href="#">Không có bài viết đã lưu</a></li>
                        @else
                        <!-- Liên kết đến trang chọn thư mục -->
                        <li><a class="dropdown-item" href="{{ route('users.posts.savePost') }}">Thư Mục Của Tôi</a></li>
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
                        <div class="col-md-4">
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
                                $images[] = filter_var($user->profile_picture, FILTER_VALIDATE_URL)
                                ? $user->profile_picture
                                : asset('storage/' . $user->profile_picture);
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
                                        <img src="{{ $image }}" alt="Ảnh" class="rounded thumbnail" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
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
                        <!-- Khung chứa tất cả ảnh của người dùng -->
                        <div class="col-md-8">
                            <h1>Chỉnh sửa hồ sơ</h1>

                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form action="{{ route('users.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Tên -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên</label>
                                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" disabled>
                                </div>

                                <!-- Ảnh đại diện -->
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Ảnh đại diện</label>
                                    <input type="file" class="form-control" id="avatar" name="avatar">
                                    @if($user->profile_picture)
                                    <img src="{{ 
                                            (filter_var(auth()->user()->profile_picture, FILTER_VALIDATE_URL)) 
                                            ? auth()->user()->profile_picture 
                                            : (auth()->user()->profile_picture 
                                                ? asset('storage/' . auth()->user()->profile_picture) 
                                                : asset('storage/images/avataricon.png')) 
                                        }}"
                                        alt="Profile picture of {{ auth()->user()->username }}"
                                        class="rounded-circle" style="width: 50px; height: 50px;" loading="lazy">
                                    @endif
                                </div>

                                <!-- Ảnh nền -->
                                <div class="mb-3">
                                    <label for="cover_image" class="form-label">Ảnh nền</label>
                                    <input type="file" class="form-control" id="cover_image" name="cover_image">
                                    @if($user->cover_image)
                                    <img src="{{ asset('storage/' . $user->cover_image) }}" alt="Ảnh nền hiện tại" class="mt-2" style="max-width: 35%; height: auto;" loading="lazy">
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary">Cập nhật hồ sơ</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h6>Liên hệ với chúng tôi</h5>
                    <p>Email: <a href="mailto:ttp6889@gmail.com">ttp6889@gmail.com</a></p>
                    <p>Phone: 038-531-5971</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>TechTalks</h5>
                    <p>&copy; {{ date('Y') }} TechTalks. All rights reserved.</p>
                </div>
                <div class="col-md-4">
                    <h6>Theo dõi chúng tôi</h5>
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-2x"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin fa-2x"></i></a>
                </div>
            </div>
            <hr>
            <p class="text-muted small">Trang web này được phát triển bởi TechTalks.</p>
    </footer>
</div>
@endsection