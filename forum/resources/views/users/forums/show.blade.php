@extends('layouts.users')

@section('title', 'Diễn dàn')

@section('content')
@include('layouts.partials.sidebar')

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