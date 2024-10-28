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

    .profile-pic {
        position: absolute;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 120px;
        height: 120px;
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
        margin-top: 80px;
        text-align: center;
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
<div class="row mx-6">

    <!-- Profile Main Section -->
    <div class="col-lg-12" style=" background-color: #fff">
        <div class="cover-image">
            <img src="{{ $user->cover_image ? asset('storage/' . $user->cover_image) : asset('storage/images/1200x300.png') }}" alt="Avatar" style="max-width:100%">
        </div>

        <!-- Ảnh đại diện -->
        <div class="profile-pic">
            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('storage/images/avataricon.png') }}" alt="Avatar">
        </div>

        <!-- Thông tin người dùng -->
        <div class="profile-details text-center">
            <h1>{{ $user->username ?? 'Tên người dùng' }}</h1>
            <p class="text-muted">{{ $user->role ?? 'Vai trò' }} | {{ $user->status ?? 'Trạng thái' }}</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="profile-nav">
            <a href="{{ url('/') }}">Home</a>
            <a href="#">Friends</a>
            <a href="{{ route('users.groups.index') }}">Groups</a>
            @if ($groups->isNotEmpty())
            @php $firstGroup = $groups->first(); @endphp
            <a href="{{ route('groups.chat', $firstGroup->id) }}">Chat
            </a>
            @endif
            <a href="#">Forums</a>
            <a href="#">More</a>
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
                    <div class="d-flex flex-wrap justify-content-center">
                        <!-- Ảnh đại diện -->
                        @if($user->profile_picture)
                        <div class="p-2">
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Ảnh đại diện" class="rounded thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        @endif

                        <!-- Ảnh nền -->
                        @if($user->cover_image)
                        <div class="p-2">
                            <img src="{{ asset('storage/' . $user->cover_image) }}" alt="Ảnh nền" class="rounded thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        @endif

                        <!-- Ảnh từ bài viết -->
                        @foreach($user->posts as $post)
                        @if($post->image)
                        <div class="p-2">
                            <img src="{{ asset('storage/' . $post->image) }}" alt="Ảnh bài viết" class="rounded thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        @endif
                        @endforeach
                    </div>
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
@endsection