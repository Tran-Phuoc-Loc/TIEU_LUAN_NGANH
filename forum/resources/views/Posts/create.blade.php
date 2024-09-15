@extends('layouts.users')

@section('title', 'Chào Mừng Đến TechTalks')

@section('content')
<div class="welcome-contents">
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show text-center" role="alert" style="position: fixed; top: 10px; right: 10px; width: 300px;">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="mt-2">
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Đăng Nhập</a>
        </div>
    </div>
    @endif

    <h1>Tạo bài viết mới</h1>

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề bài viết</label>
            <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}">
            @error('title')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Nội dung bài viết</label>
            <textarea name="content" class="form-control" id="content" rows="5">{{ old('content') }}</textarea>
            @error('content')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Hình ảnh (tuỳ chọn)</label>
            <input type="file" name="image" class="form-control" id="image">
            @error('image')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái bài viết</label>
            <select name="status" class="form-select" id="status">
                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
            </select>
            @error('status')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Lưu Bài Viết</button>
        </div>
    </form>

    @if(isset($post) && $post->status === 'draft')
    <form action="{{ route('posts.publish', $post->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success">Xuất Bản</button>
    </form>
    @endif
</div>
@endsection