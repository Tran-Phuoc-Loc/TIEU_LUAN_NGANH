@extends('layouts.admin')

@section('title', 'Chi tiết nhóm')

@section('content')
<div class="container mt-4">
    <!-- Tiêu đề -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết nhóm: <span class="text-primary">{{ $group->name }}</span></h2>
        <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <!-- Avatar và mô tả nhóm -->
    <div class="row mb-4">
        <div class="col-md-4 text-center">
            @if ($group->avatar)
            <img src="{{ asset('storage/' . $group->avatar) }}"
                alt="Avatar của nhóm {{ $group->name }}"
                class="img-fluid rounded shadow-sm"
                style="max-width: 100%; height: auto;">
            @else
            <div class="bg-light p-3 rounded">
                <em>Nhóm chưa có avatar.</em>
            </div>
            @endif
        </div>
        <div class="col-md-8">
            <p><strong>Mô tả:</strong> {{ $group->description }}</p>
            <p><strong>Chủ nhóm:</strong> {{ $group->creator->username ?? 'Không rõ' }}</p>
        </div>
    </div>

    <!-- Thống kê nhóm -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thống kê nhóm</h5>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item"><strong>Số lượng thành viên:</strong> {{ $group->members->count() }}</li>
                <li class="list-group-item"><strong>Số lượng tin nhắn:</strong> {{ $group->chats_count }}</li>
                <li class="list-group-item"><strong>Ngày tạo nhóm:</strong> {{ $group->created_at->format('d/m/Y') }}</li>
                <li class="list-group-item"><strong>Ngày hoạt động gần nhất:</strong> {{ $group->chats()->latest()->first()?->created_at->format('d/m/Y H:i') ?? 'Chưa có hoạt động' }}</li>
            </ul>
        </div>
    </div>

    <!-- Bài viết của nhóm -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Bài viết của nhóm</h5>
        </div>
        <div class="card-body">
            @if ($groupPosts->isNotEmpty())
            <ul class="list-group">
                @foreach ($groupPosts as $post)
                <li class="list-group-item">
                    <h6 class="mb-1">{{ $post->title }}</h6>
                    <p class="mb-1 text-muted">{{ Str::limit($post->content, 100) }}</p>
                    <small>Đăng bởi: <strong>{{ $post->user->username }}</strong> vào ngày {{ $post->created_at->format('d/m/Y') }}</small>
                </li>
                @endforeach
            </ul>
            <!-- Hiển thị nút phân trang -->
            <div class="mt-3">
                {{ $groupPosts->links() }}
            </div>
            @else
            <p class="text-muted">Nhóm chưa có bài viết.</p>
            @endif
        </div>
    </div>

    <!-- Thành viên của nhóm -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Thành viên của nhóm</h5>
        </div>
        <div class="card-body">
            @if ($groupMembers->isNotEmpty())
            <ul class="list-group">
                @foreach ($groupMembers as $member)
                <li class="list-group-item">
                    <strong>{{ $member->username }}</strong> ({{ $member->email }})
                </li>
                @endforeach
            </ul>
            <!-- Hiển thị nút phân trang -->
            <div class="mt-3">
                {{ $groupMembers->links() }}
            </div>
            @else
            <p class="text-muted">Nhóm chưa có thành viên.</p>
            @endif
        </div>
    </div>
</div>
@endsection