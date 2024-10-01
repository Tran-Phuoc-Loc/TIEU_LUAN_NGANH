@extends('layouts.admin')

@section('content')
<div class="container">
    <!-- Thông báo cảnh báo dành cho admin -->
    <div class="alert alert-warning">
        Bạn đang chỉnh sửa bài viết với tư cách là quản trị viên. Hãy cẩn thận khi thay đổi nội dung.
    </div>
    <h2>Chỉnh sửa bài viết(Admin)</h2>

    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="form-group">
            <label for="title">Tiêu đề</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $post->title) }}" required>
        </div>

        <div class="form-group">
            <label for="content">Nội dung</label>
            <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content', $post->content) }}</textarea>
        </div>

        <div class="form-group">
            <label for="category_id">Danh mục</label>
            <select class="form-control" id="category_id" name="category_id" required>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $post->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="status">Trạng thái</label>
            <select class="form-control" id="status" name="status" required>
                <option value="draft" {{ $post->status == 'draft' ? 'selected' : '' }}>Nháp</option>
                <option value="published" {{ $post->status == 'published' ? 'selected' : '' }}>Đã đăng</option>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Ảnh đại diện</label>
            <input type="file" class="form-control-file" id="image" name="image">
            @if($post->image_url)
            <img src="{{ asset('storage/' . $post->image_url) }}" alt="Current Image" style="max-width: 200px; margin-top: 10px;">
            @endif
        </div>

        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Hủy bỏ</a>
    </form>
</div>
@endsection