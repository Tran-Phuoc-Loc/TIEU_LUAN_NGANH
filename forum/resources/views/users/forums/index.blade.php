@extends('layouts.users')

@section('title', 'Diễn dàn')

@section('content')
@include('layouts.partials.sidebar')

<!-- Nội dung bài viết chính -->
<div class="col-lg-6 col-md-7 offset-lg-2 content-col" style="border: 2px solid #e1e1e2; background-color:#fff; margin-left: 17%;">
    <h2>Bài Viết</h2>
    <!-- Form bộ lọc -->
    <form action="{{ route('forums.index') }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="title" class="form-control" placeholder="Tìm theo tiêu đề" value="{{ request('title') }}">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-control">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="sort" class="form-control">
                    <option value="new" {{ request('sort') === 'new' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="old" {{ request('sort') === 'old' ? 'selected' : '' }}>Cũ nhất</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Lọc</button>
    </form>

    <a href="{{ route('forums.create') }}" class="btn btn-primary">Thêm Bài Viết Mới</a>
    <hr>

    @if(isset($posts) && $posts->isNotEmpty())
    <ul>
        @foreach ($posts as $post)
        <li>
            <div class="d-flex justify-content-between align-items-center">
                <h4>
                    <a href="{{ route('forums.show', $post->id) }}" style="text-decoration: none; color: #007bff;">
                        {{ $post->title }}
                    </a>
                </h4>

                <!-- Hiển thị menu dropdown cho tác giả hoặc admin -->
                @if(auth()->user()->id === $post->user_id || auth()->user()->role === 'admin')
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Tùy chọn
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item" href="{{ route('forums.edit', $post->id) }}">Chỉnh sửa</a>
                        </li>
                        <li>
                            <form action="{{ route('forums.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">Xóa</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endif
            </div>

            <p>{!! Str::limit($post->content, 200) !!}</p>
            <p><small>Viết bởi: {{ $post->user->username ?? 'Không có tên' }} - {{ $post->created_at->format('d-m-Y H:i') }}</small></p>

            @if($post->file_path)
            <div class="mb-3">
                <a href="{{ asset('storage/' . $post->file_path) }}" class="btn btn-info" download>Tải xuống tài liệu</a>
            </div>
            @endif

            <hr>
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
                    <p>Thời gian cập nhật: {{ $post->updated_at->format('d/m/Y H:i') }}</p>
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
</div>
</div>
</div>
@endsection