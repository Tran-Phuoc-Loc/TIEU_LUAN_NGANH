@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')

@section('content')
<div class="container mt-4">
    <h2>Chi tiết người dùng: {{ $user->username }}</h2>

    <!-- Chia thành 2 cột: Cột bên trái cho thông tin người dùng, cột bên phải cho ảnh -->
    <div class="row mb-3">
        <!-- Cột bên trái: Thông tin người dùng -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Vai trò:</strong> {{ $user->role }}</p>
                    <p><strong>Trạng thái:</strong> {{ ucfirst($user->status) }}</p>
                    <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                    <p><strong>Số bài viết:</strong> {{ $postCount }}</p>
                    <p><strong>Số nhóm:</strong> {{ $groupCount }}</p>
                    <p><strong>Số sản phẩm:</strong> {{ $productCount }}</p>
                </div>
            </div>
        </div>

        <!-- Cột bên phải: Ảnh của người dùng -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-center mb-3">Tất cả ảnh của {{ $user->username }}</h5>

                    <!-- Kiểm tra nếu không có ảnh -->
                    @php
                    $hasImages = $user->profile_picture || $user->cover_image || $user->posts->where('status', 'published')->whereNotNull('image')->count() > 0;
                    @endphp

                    @if($hasImages)
                    <div class="d-flex flex-wrap justify-content-center" style="max-height: 300px; overflow-y: auto;">
                        <!-- Ảnh đại diện -->
                        @if($user->profile_picture)
                        <div class="p-1">
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Ảnh đại diện" class="rounded thumbnail" style="width: 90px; height: 90px; object-fit: cover;">
                        </div>
                        @endif

                        <!-- Ảnh nền -->
                        @if($user->cover_image)
                        <div class="p-1">
                            <img src="{{ asset('storage/' . $user->cover_image) }}" alt="Ảnh nền" class="rounded thumbnail" style="width: 90px; height: 90px; object-fit: cover;">
                        </div>
                        @endif

                        <!-- Ảnh từ bài viết đã published -->
                        @foreach($user->posts->where('status', 'published') as $post)
                        @if($post->image_url)
                        <div class="p-1">
                            <img src="{{ asset('storage/' . $post->image_url) }}" alt="Ảnh bài viết" class="rounded thumbnail" style="width: 90px; height: 90px; object-fit: cover;">
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @else
                    <p class="text-center text-muted">Người dùng chưa có hình ảnh đã đăng.</p>
                    @endif

                    <!-- Thông báo nếu có ảnh trong trạng thái draft -->
                    @if($user->posts->where('status', 'draft')->pluck('image')->isNotEmpty())
                    <p class="text-center text-warning mt-3">Một số ảnh đã bị ẩn vì bài viết đang ở trạng thái "Draft".</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection