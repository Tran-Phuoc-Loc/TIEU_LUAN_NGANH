@extends('layouts.users')

@section('title', 'Chỉnh Sửa Bài Viết')

@section('content')
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
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
                
                <div class="form-group">
                    <label for="group">Nhóm</label>
                    <select class="form-control" id="group" name="group_id" required>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ $post->group_id == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
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

                <!-- Trường tải lên 1 ảnh hoặc video -->
                <div class="mb-3">
                    <label for="media_single" class="form-label">Tải lên 1 ảnh hoặc video</label>
                    @if ($post->image_url)
                    @if ($post->isImage())
                    <!-- Hiển thị ảnh hiện có -->
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $post->image_url) }}" alt="Current Image" class="img-fluid" style="max-width: 200px;">
                    </div>
                    @elseif ($post->isVideo())
                    <!-- Hiển thị video hiện có -->
                    <div class="mb-2">
                        <video controls class="video-player" style="max-width: 400px;">
                            <source src="{{ asset('storage/' . $post->image_url) }}" type="video/mp4">
                            Trình duyệt của bạn không hỗ trợ video.
                        </video>
                    </div>
                    @endif
                    @endif

                    <input type="file" name="media_single" class="form-control" accept="image/*,video/*" id="media_single">
                    @error('media_single')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Trường tải lên nhiều ảnh -->
                <div class="mb-3">
                    <label for="media_multiple" class="form-label">Tải lên nhiều ảnh</label>

                    @if ($post->postImages && $post->postImages->isNotEmpty())
                    <div class="mb-3">
                        <label>Ảnh hiện có:</label>
                        <div class="d-flex flex-wrap">
                            @foreach ($post->postImages as $image)
                            <div class="me-2 mb-2" style="position: relative;">
                                <img src="{{ asset('storage/' . $image->file_path) }}" alt="Post Image" class="img-fluid" style="max-width: 150px; max-height: 150px;">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <input type="file" name="media_multiple[]" class="form-control" accept="image/*" multiple id="media_multiple">
                    @error('media_multiple')
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