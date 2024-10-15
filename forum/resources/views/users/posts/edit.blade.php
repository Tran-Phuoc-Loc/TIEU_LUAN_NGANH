@extends('layouts.users')

@section('title', 'Chỉnh Sửa Bài Viết')

@section('content')
<div class="row">
    <div class="post-container">
        <h1>Chỉnh Sửa Bài Viết</h1>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="category">Danh mục</label>
                <select class="form-control" id="category" name="category_id" required>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $post->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề bài viết</label>
                <input type="text" name="title" class="form-control" id="title" value="{{ old('title', $post->title) }}" required>
                @error('title')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Nội dung bài viết</label>
                <textarea name="content" class="form-control" id="content" rows="5" required>{{ old('content', $post->content) }}</textarea>
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
                <select name="status" class="form-select" id="status" required>
                    <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                </select>
                @error('status')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Cập Nhật Bài Viết</button>
            </div>

        </form>
    </div>
</div>
@endsection