@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h2>Dashboard Overview</h2>
    <div class="row">
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Tổng số người dùng</h5>
                    <p class="card-text">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Bài viết</h5>
                    <p class="card-text">{{ $totalPosts }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Báo cáo vi phạm</h5>
                    <p class="card-text"></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Số người đăng ký hôm nay</h5>
                    <p class="card-text">{{ $newRegistrationsToday }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3>Hoạt động gần đây</h3>
    <ul class="list-group">
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
