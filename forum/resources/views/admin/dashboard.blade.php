@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h2>Dashboard Overview</h2>
    <div class="row">
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Tổng số người dùng</h5>
                    <p class="card-text">150</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Bài viết</h5>
                    <p class="card-text">200</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Lượt yêu thích</h5>
                    <p class="card-text">15</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Số người đăng ký hôm nay</h5>
                    <p class="card-text">8</p>
                </div>
            </div>
        </div>
    </div>

    <h3>Hoạt động gần đây</h3>
    <ul class="list-group">
        <li class="list-group-item">
            <strong>Student123</strong> Tạo bài viết mới : <em>"Mẹo cho kỳ thi"</em>
            <span class="badge bg-primary float-end">2 hours ago</span>
        </li>
        <li class="list-group-item">
            <strong>Student456</strong> Đã cập nhật ảnh đại diện của họ.
            <span class="badge bg-primary float-end">5 hours ago</span>
        </li>
        <li class="list-group-item">
            <strong>Student789</strong> đã bình luận về một bài đăng.
            <span class="badge bg-primary float-end">1 day ago</span>
        </li>
    </ul>
@endsection
