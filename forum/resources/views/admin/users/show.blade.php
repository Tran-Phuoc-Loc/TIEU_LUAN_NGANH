@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Chi tiết người dùng: {{ $user->username }}</h2>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row mb-3">
        <!-- Thông tin người dùng -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <!-- Avatar của người dùng -->
                    <div class="text-center mb-3">
                        <h5>Avatar của {{ $user->username }}</h5>
                        <!-- Hiển thị avatar -->
                        <div class="text-center">
                            @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Avatar" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                            <img src="https://via.placeholder.com/150" alt="Avatar" class="rounded-circle" style="width: 150px; height: 150px;">
                            @endif
                        </div>
                    </div>
                    <!-- Thông tin người dùng -->
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Vai trò:</strong> {{ $user->role }}</p>
                    <p><strong>Trạng thái:</strong> {{ ucfirst($user->status) }}</p>
                    <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                    <p><strong>Số bài viết:</strong> {{ $postCount }}</p>
                    <p><strong>Số nhóm:</strong> {{ $groupCount }}</p>
                    <p><strong>Số sản phẩm:</strong> {{ $productCount }}</p>

                    <hr>

                    <hr>

                    <!-- Nhóm tham gia -->
                    <h5 class="mt-4">Nhóm tham gia:</h5>
                    @if($user->groups->isNotEmpty())
                    <ul>
                        @foreach($user->groups as $group)
                        <li>{{ $group->name }}</li>
                        @endforeach
                    </ul>
                    @else
                    <p>Người dùng chưa tham gia nhóm nào.</p>
                    @endif

                    <hr>

                    <!-- Sản phẩm đã đăng -->
                    <h5 class="mt-4">Sản phẩm đã đăng:</h5>
                    @if($user->products->isNotEmpty())
                    <ul>
                        @foreach($user->products as $product)
                        <li>{{ $product->name }}</li>
                        @endforeach
                    </ul>
                    @else
                    <p>Người dùng chưa đăng sản phẩm nào.</p>
                    @endif

                    <hr>

                    <!-- Bài viết đã đăng -->
                    <h5 class="mt-4">Bài viết đã đăng:</h5>
                    <a class="nav-link" href="{{ route('admin.posts.index') }}">
                        <i class="fas fa-file-alt me-2"></i> Posts
                    </a>
                </div>
            </div>
        </div>

        <!-- Cột bên phải: Ảnh và video của người dùng -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-center mb-3">Tất cả ảnh của {{ $user->username }}</h5>

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

                    <!-- Hiển thị ảnh -->
                    <div class="d-flex flex-wrap gap-2 justify-content-center" style="overflow-y: auto; max-height: 400px;">
                        @foreach ($images as $image)
                        <div class="thumbnail-wrapper" style="width: 100px; height: 100px; position: relative;">
                            <img src="{{ $image }}" alt="Ảnh" class="rounded thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        @endforeach
                    </div>

                    <!-- Hiển thị video nếu có -->
                    @if(!empty($videos))
                    <div class="d-flex flex-wrap gap-2 justify-content-center" style="overflow-y: auto; max-height: 400px;">
                        @foreach ($videos as $video)
                        <div class="video-wrapper" style="width: 200px; height: 150px; position: relative;">
                            <video controls style="width: 100%; height: 100%;">
                                <source src="{{ $video }}" type="video/{{ pathinfo($video, PATHINFO_EXTENSION) }}">
                                Trình duyệt của bạn không hỗ trợ video.
                            </video>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Thông báo nếu có ảnh trong trạng thái draft -->
                    @if($user->posts->where('status', 'draft')->pluck('image_url')->isNotEmpty())
                    <p class="text-center text-warning mt-3">Một số ảnh đã bị ẩn vì bài viết đang ở trạng thái "Draft".</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection