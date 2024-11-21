@extends('layouts.users')

@section('title', 'Diễn dàn')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar điều hướng cố định bên trái -->
        <div class="col-lg-2 col-md-1 sidebar d-none d-md-block" style="background-color: #fff; position: fixed; height: 100vh; overflow-y: auto;">
            <div class="vertical-navbar">
                <!-- Thông tin người dùng -->
                <div class="user-info text-center mb-4" style="background-color: black; background-image: linear-gradient(135deg, #52545f 0%, #383a45 50%);">
                    @if(auth()->check())
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
                            @if ($groups->isNotEmpty())
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

        <!-- Nội dung bài viết chính -->
        <div class="col-lg-6 col-md-7 offset-lg-2 content-col" style="border: 2px solid #007bff; background-color:#fff; margin-left: 17%;">
            <h2>{{ $forumPost->title }}</h2>
            <p>{!! $forumPost->content !!}</p>

            <p><small>Viết bởi: {{ $forumPost->user->username ?? 'Không có tên' }} - {{ $forumPost->created_at->format('d-m-Y H:i') }}</small></p>

            @if($forumPost->file_path)
            <div class="mb-3">
                <a href="{{ asset('storage/' . $forumPost->file_path) }}" class="btn btn-info" download>Tải xuống tài liệu</a>
            </div>
            @endif

            <hr>

            <h3>Bình luận</h3>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($forumPost->comments->isNotEmpty())
            <div class="comments">
                @foreach ($forumPost->comments as $comment)
                <div class="comment mb-3">
                    <strong>{{ $comment->user->username }}</strong>
                    <p>{{ $comment->content }}</p>
                    <small>{{ $comment->created_at->diffForHumans() }}</small>
                </div>
                @endforeach
            </div>
            @else
            <p>Chưa có bình luận nào.</p>
            @endif

            <hr>

            <h4>Đặt câu hỏi</h4>
            <form action="{{ route('forums.comments.store', $forumPost->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <textarea name="content" class="form-control" placeholder="Đặt câu hỏi..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Gửi</button>
            </form>
        </div>
        <!-- Sidebar danh mục diễn đàn bên phải -->
        <div class="col-lg-3 col-md-3 mt-lg-0 right-sidebar" style="background-color: #fff; width: 32%; margin-left: auto;">
            <h1>Diễn Đàn</h1>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Danh mục -->
            <h2>Danh Mục</h2>
            <ul>
                @foreach($categories as $category)
                <li>
                    <strong>{{ $category->name }}</strong>
                    @if($category->posts->isNotEmpty())
                    <ul>
                        @foreach($category->posts as $post)
                        <li>
                            <a href="{{ route('forums.show', $post->id) }}">{{ $post->title }}</a> -
                            <em>{{ $post->user->username ?? 'Không có tên' }}</em>
                            ({{ $post->created_at->diffForHumans() }})
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </li>
                @endforeach
            </ul>

            <!-- Bài viết mới nhất -->
            <h2>Bài Viết Mới Nhất</h2>
            <ul>
                @foreach($latestPosts as $post)
                <li>
                    <a href="{{ route('forums.show', $post->id) }}">{{ $post->title }}</a> -
                    <em>{{ $post->user->username ?? 'Không có tên' }}</em>
                    ({{ $post->created_at->diffForHumans() }})
                </li>
                @endforeach
            </ul>

            <a href="{{ route('forums.create') }}" class="btn btn-primary">Thêm Bài Viết Mới</a>
        </div>
    </div>
</div>
@endsection