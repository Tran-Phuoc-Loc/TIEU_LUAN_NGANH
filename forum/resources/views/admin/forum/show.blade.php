@extends('layouts.admin')

@section('content')
    <h1>Chi tiết bài viết</h1>

    <div class="mb-3">
        <a href="{{ route('admin.forum.index') }}" class="btn btn-primary">Quay lại danh sách bài viết</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>{{ $post->title }}</h4>
        </div>
        <div class="card-body">
            <p><strong>Danh mục:</strong> {{ $post->category->name }}</p>
            <p><strong>Người tạo:</strong> {{ $post->user->username }}</p>
            <!-- Hiển thị avatar người tạo bài viết -->
            <div>
                <strong>Avatar:</strong>
                <img src="{{ $post->user->profile_picture ? (filter_var($post->user->profile_picture, FILTER_VALIDATE_URL) ? $post->user->profile_picture : asset('storage/' . $post->user->profile_picture)) : asset('storage/images/avataricon.png') }}" alt="Avatar" class="rounded thumbnail" loading="lazy" style="width: 50px; height: 50px; object-fit: cover;">
            </div>
            <p><strong>Nội dung:</strong></p>
            <div>{!! $post->content !!}</div>

            @if($post->file_path)
                <p><strong>Tài liệu đính kèm:</strong></p>
                <a href="{{ asset('storage/' . $post->file_path) }}" target="_blank" class="btn btn-success">
                    Tải xuống tài liệu
                </a>
            @endif
        </div>
    </div>
@endsection
