@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h2>Tổng quan Dashboard</h2>
<div class="row">
    <!-- Tổng số người dùng -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Tổng số người dùng</h5>
                <p class="card-text">{{ $totalUsers }}</p>
            </div>
        </div>
    </div>

    <!-- Tổng số bài viết -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Bài viết</h5>
                <p class="card-text">{{ $totalPosts }}</p>
            </div>
        </div>
    </div>

    <!-- Tổng số báo cáo vi phạm -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h5 class="card-title">Báo cáo vi phạm</h5>
                <p class="card-text">{{ $totalReports }}</p>
            </div>
        </div>
    </div>

    <!-- Số người đăng ký hôm nay -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Số người đăng ký hôm nay</h5>
                <p class="card-text">{{ $newRegistrationsToday }}</p>
            </div>
        </div>
    </div>
</div>

<h3>Những người dùng hoạt động nhiều nhất</h3>
<ul class="list-group mb-4">
    @foreach ($mostActiveUsers as $user)
    <li class="list-group-item">
        <strong>{{ $user->username }}</strong> - Bài viết: {{ $user->posts_count }} | Bình luận: {{ $user->comments_count }}
    </li>
    @endforeach
</ul>

<h3>Số nhóm được tạo ra</h3>
<p>Tổng số nhóm: {{ $totalGroups }}</p>

<h3>Tỷ lệ bài viết theo trạng thái</h3>
<ul class="list-group mb-4">
    @foreach ($postStatusCount as $status)
    <li class="list-group-item">
        <strong>{{ ucfirst($status->status) }}:</strong> {{ $status->count }} bài viết
    </li>
    @endforeach
</ul>

<h3>Danh mục có nhiều bài viết nhất</h3>
<ul class="list-group mb-4">
    @foreach ($topCategories as $category)
    <li class="list-group-item">
        <strong>{{ $category->name }}</strong> - {{ $category->posts_count }} bài viết
    </li>
    @endforeach
</ul>

<h3>Hoạt động gần đây</h3>
<ul class="list-group mb-4">
    @foreach ($recentActivities['posts'] as $post)
    <li class="list-group-item">
        <strong>{{ $post->user->username }}</strong> Tạo bài viết mới:
        <em>"{{ $post->title }}"</em>
        <span class="badge bg-primary float-end">{{ $post->created_at->diffForHumans() }}</span>
    </li>
    @endforeach
    @foreach ($recentActivities['comments'] as $comment)
    <li class="list-group-item">
        <strong>{{ $comment->user->username }}</strong> đã bình luận về một bài đăng.
        <span class="badge bg-primary float-end">{{ $comment->created_at->diffForHumans() }}</span>
    </li>
    @endforeach
</ul>

@endsection