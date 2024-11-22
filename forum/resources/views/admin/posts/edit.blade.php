@extends('layouts.admin')

@section('content')
<div class="container">
    <!-- Thông báo cảnh báo dành cho admin -->
    <div class="alert alert-warning">
        Bạn đang chỉnh sửa bài viết với tư cách là quản trị viên. Hãy cẩn thận khi thay đổi nội dung.
        @if ($post->edit_status === 'pending')
        <strong> Lưu ý: Bài viết này đang chờ phê duyệt!</strong>
        @endif
    </div>

    <h2 class="mb-4">Chỉnh sửa bài viết (Admin)</h2>

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

        <!-- Yêu cầu sửa bài viết -->
        <div class="form-group mb-3">
            <label for="edit_reason">Yêu cầu sửa bài viết</label>
            <textarea class="form-control" id="edit_reason" name="edit_reason" rows="3">{{ old('edit_reason', $post->edit_reason) }}</textarea>
        </div>

        <div class="form-group">
            <label for="title">Tiêu đề</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $post->title) }}" required>
        </div>

        <!-- Nội dung bài viết -->
        <div class="form-group mb-3">
            <label for="content">Nội dung</label>
            <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content', $post->content) }}</textarea>
        </div>

        <!-- Danh mục -->
        <div class="form-group mb-3">
            <label for="category_id">Danh mục</label>
            <select class="form-control" id="category_id" name="category_id" required>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ (old('category_id', $post->category_id) == $category->id) ? 'selected' : '' }}>
                    {{ e($category->name) }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Dropdown nhóm -->
        <div class="form-group mb-3">
            <label for="group_id">Nhóm</label>
            <select class="form-control" id="group_id" name="group_id">
                <option value="">Không thuộc nhóm</option>
                @foreach($groups as $group)
                <option value="{{ $group->id }}" {{ (old('group_id', $post->group_id) == $group->id) ? 'selected' : '' }}>
                    {{ e($group->name) }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Trạng thái bài viết -->
        <div class="form-group mb-3">
            <label for="status">Trạng thái</label>
            <select class="form-control" id="status" name="status" required>
                <option value="draft" {{ (old('status', $post->status) == 'draft') ? 'selected' : '' }}>Nháp</option>
                <option value="published" {{ (old('status', $post->status) == 'published') ? 'selected' : '' }}>Đã đăng</option>
            </select>
        </div>

        <!-- Trường tải lên 1 ảnh hoặc video -->
        <div class="form-group mb-3">
            <label for="media_single" class="form-label">Ảnh hoặc video Sinh viên tải lên</label>
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
        <div class="form-group mb-3">
            <label for="media_multiple" class="form-label">Tải lên nhiều ảnh</label>

            <!-- Hiển thị ảnh đã tải lên trước đó (nếu có) -->
            @if ($post->postImages && $post->postImages->isNotEmpty())
            <div class="mb-3">
                <label>Ảnh được sinh viên đăng tải:</label>
                <div class="d-flex flex-wrap">
                    @foreach ($post->postImages as $image)
                    <div class="me-2 mb-2" style="position: relative;">
                        <img src="{{ asset('storage/' . $image->file_path) }}" alt="Post Image" class="img-fluid" style="max-width: 150px; max-height: 150px;">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Input cho phép admin tải lên ảnh mới -->
            <input type="file" name="media_multiple[]" class="form-control" accept="image/*" multiple id="media_multiple">

            @error('media_multiple')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nút gửi và hủy -->
        <div class="form-group text-right">
            <button type="submit" class="btn btn-success">Cập nhật</button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Hủy bỏ</a>
        </div>
    </form>
</div>
@endsection