@extends('layouts.users')
@section('title', 'bài viết đã thích')

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
                    <img src="{{ $user->cover_image ? asset('storage/' . $user->cover_image) : asset('storage/images/covers/1200x300.png') }}" alt="Avatar" style="max-width:100%" class="rounded thumbnail">
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
                            alt="Avatar" class="rounded thumbnail">
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

                    <div class="col-lg-12 col-md-12 content-col" style="border: 2px solid #c8ccd0; background-color:#fff; text-align:center;">
                        <div class="post-container mb-4">
                            <div class="row">
                                <h2>Bài Viết Đã Thích</h2>

                                @if($favoritePosts->isEmpty())
                                <p>Bạn chưa thích bài viết nào.</p>
                                @else
                                <ul class="list-group">
                                    @foreach($favoritePosts as $post)
                                    <li class="list-group-item">
                                        <a href="{{ route('users.index', ['post' => $post->id]) }}">{{ $post->title }}</a>

                                        <span class="badge bg-primary float-end">{{ $post->likes_count }} lượt thích</span>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endsection