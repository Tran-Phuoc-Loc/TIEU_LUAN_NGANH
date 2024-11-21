@extends('layouts.users')

@section('title', 'Tạo bài viết mới')

@section('content')
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert" style="position: fixed; top: 10px; right: 10px; width: 300px;">
                {{ session('error') }}
            </div>
            @endif

            <div class="card p-4 mb-4" style="border: 1px solid #ddd;">
                <h2 class="text-center mb-4">Tạo bài viết mới</h2>
                <form action="{{ route('users.posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if(isset($group) && $group instanceof \App\Models\Group)
                    <p>Group ID: {{ $group->id }}</p>
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    @endif

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Danh mục</label>
                        <select name="category_id" class="form-select" id="category_id" required>
                            <option value="">Chọn danh mục</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề bài viết</label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}">
                        @error('title')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Trường nội dung bài viết -->
                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung bài viết</label>
                        <textarea name="content" class="form-control" id="content" rows="5" placeholder="Chia sẻ suy nghĩ của bạn...">{{ old('content') }}</textarea>
                        @error('content')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Trường tải lên 1 ảnh hoặc video -->
                    <div class="mb-3">
                        <label for="media_single" class="form-label">Tải lên 1 ảnh hoặc video</label>
                        <input type="file" name="media_single" class="form-control" accept="image/*,video/*" id="media_single">
                        @error('media_single')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Trường tải lên nhiều ảnh -->
                    <div class="mb-3">
                        <label for="media_multiple" class="form-label">Tải lên nhiều ảnh</label>
                        <input type="file" name="media_multiple[]" class="form-control" accept="image/*" multiple id="media_multiple">
                        @error('media_multiple')
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

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Lưu Bài Viết</button>
                    </div>
                </form>

                @if(isset($post) && $post->status === 'draft')
                <form action="{{ route('posts.publish', $post->id) }}" method="POST" class="text-center mt-3">
                    @csrf
                    <button type="submit" class="btn btn-success">Xuất Bản</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection