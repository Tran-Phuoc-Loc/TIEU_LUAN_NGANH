@extends('layouts.users')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar điều hướng cố định bên trái -->
        <div class="col-lg-2 col-md-1 sidebar d-none d-md-block" style="background-color: #fff; position: fixed; height: 100vh; overflow-y: auto;">
            <div class="vertical-navbar">
                <!-- Thông tin người dùng -->
                <div class="user-info text-center mb-4" style="background-color: black;background-image: linear-gradient(135deg, #52545f 0%, #383a45 50%);">
                    @if(auth()->check())
                    <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('storage/images/avataricon.png') }}"
                        alt="Profile picture of {{ auth()->user()->username }}"
                        class="rounded-circle" style="width: 45px; height: 50px;">
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
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="bi bi-pencil"></i>
                                <span class="d-none d-lg-inline">Bài viết của bạn</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">
                                <i class="bi bi-folder"></i>
                                <span class="d-none d-lg-inline">Danh mục</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forums.index') }}">
                                <i class="bi bi-chat-dots"></i>
                                <span class="d-none d-lg-inline">Diễn đàn</span>
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
                            @if ($groups->isNotEmpty())
                            @php $firstGroup = $groups->first(); @endphp
                            <a href="{{ route('groups.chat', $firstGroup->id) }}">
                                <i class="fas fa-comment-sms" style="font-size: 40px"></i>
                                <span class="d-none d-lg-inline">Tin nhắn</span>
                            </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Nội dung bài viết chính -->
        <div class="col-lg-6 col-md-7 offset-lg-2 content-col" style="border: 2px solid #007bff; background-color:#fff; margin-left: 17%;">
            <h2>Bài Viết</h2>
            @if(isset($posts) && $posts->isNotEmpty())
            <ul>
                @foreach ($posts as $post)
                <li>
                    <h4><a href="{{ route('forums.show', $post->id) }}" style="text-decoration: none; color: #007bff;">
                            {{ $post->title }}
                        </a></h4>
                    <p>{{ $post->content }}</p>
                    <p><small>Viết bởi: {{ $post->user->username ?? 'Không có tên' }} - {{ $post->created_at->format('d-m-Y H:i') }}</small></p>
                </li>
                @endforeach
            </ul>
            @else
            <p>Hiện chưa có bài viết nào trong danh mục này.</p>
            @endif
        </div>

        <!-- Sidebar danh mục diễn đàn bên phải -->
        <div class="col-lg-3 col-md-3 mt-lg-0 right-sidebar" style="background-color: #fff; width: 32%; margin-left: auto;">
            <h1>Diễn Đàn</h1>
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <h2>Danh Mục</h2>
            <ul>
                @foreach($categories as $category)
                <li>
                    <strong>{{ $category->name }}</strong>
                    <ul>
                        @foreach($category->posts as $post)
                        <li>
                            <a href="">{{ $post->title }}</a> -
                            <em>{{ $post->user->username ?? 'Không có tên' }}</em>
                            ({{ $post->created_at->diffForHumans() }})
                        </li>
                        @endforeach
                    </ul>
                </li>
                @endforeach
            </ul>
            <a href="{{ route('forums.create') }}" class="btn btn-primary">Thêm Bài Viết Mới</a>
        </div>
    </div>
</div>
@endsection