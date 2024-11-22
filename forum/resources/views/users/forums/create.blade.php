@extends('layouts.users')

@section('title', 'Tạo bài viết diễn dàn')

@section('content')
@include('layouts.partials.sidebar')

<!-- Nội dung bài viết chính -->
<div class="col-lg-6 col-md-7 offset-lg-2 content-col" style="border: 2px solid #007bff; background-color:#fff; margin-left: 17%;">
    <h1>Thêm Bài Viết Mới</h1>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('forums.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề bài viết</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Nhập tiêu đề" value="{{ old('title') }}" required>
            @error('title')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="forum_category_id" class="form-label">Danh mục</label>
            <select class="form-select" id="forum_category_id" name="forum_category_id" required>
                <option value="">Chọn danh mục</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('forum_category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
            @error('forum_category_id')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="content">Nội dung</label>
            <textarea class="form-control" id="content" name="content" rows="5"></textarea>
        </div>

        <div class="mb-3">
            <label for="file" class="form-label">Tải lên tài liệu/ảnh</label>
            <input type="file" class="form-control" id="file" name="file" accept=".pdf,.docx,.pptx,image/*">
        </div>

        <button type="submit" class="btn btn-primary">Đăng Bài Viết</button>
        <a href="{{ route('forums.index') }}" class="btn btn-secondary">Hủy</a>
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