@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<style>
    .card {
        border-left: 5px solid !important;
        /* Viền bên trái */
    }

    .card-users {
        border-left-color: #007bff !important;
        /* Màu cho card người dùng */
    }

    .card-posts {
        border-left-color: #28a745 !important;
        /* Màu cho card bài viết */
    }

    .card-reports {
        border-left-color: #dc3545 !important;
        /* Màu cho card báo cáo */
    }

    .card-registrations {
        border-left-color: #ffc107 !important;
        /* Màu cho card đăng ký hôm nay */
    }
</style>
<div class="container mt-4">
    <h1 class="mb-4">Welcome back, {{ Auth::user()->username }}!</h1>
    <p id="currentDateTime" style="color: blue;"></p> <!-- Phần này sẽ hiển thị ngày giờ -->

    <!-- Hàng cho số liệu thống kê tóm tắt -->
    <div class="row">
        <!-- Card Tổng số người dùng -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-sm border-0 text-center hover-card card-users">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h6 class="card-title">Tổng số người dùng</h6>
                    <h2>{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>

        <!-- Card Tổng số bài viết -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-sm border-0 text-center hover-card card-posts">
                <div class="card-body">
                    <i class="fas fa-file-alt fa-2x text-success mb-2"></i>
                    <h6 class="card-title">Tổng số bài viết</h6>
                    <h2>{{ $totalPosts }}</h2>
                </div>
            </div>
        </div>

        <!-- Card Tổng số báo cáo vi phạm -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-sm border-0 text-center hover-card card-reports">
                <div class="card-body">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                    <h6 class="card-title">Báo cáo vi phạm</h6>
                    <h2>{{ $totalReports }}</h2>
                </div>
            </div>
        </div>

        <!-- Card Số người đăng ký hôm nay -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-sm border-0 text-center hover-card card-registrations">
                <div class="card-body">
                    <i class="fas fa-user-plus fa-2x text-warning mb-2"></i>
                    <h6 class="card-title">Đăng ký hôm nay</h6>
                    <h2>{{ $newRegistrationsToday }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Phần dành cho trạng thái bài đăng và hoạt động gần đây -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <h5 class="card-title mb-0">Hoạt động gần đây</h5>
                        <!-- Bộ chọn khoảng thời gian -->
                        <select id="timeRangeFilter" class="form-select w-auto">
                            <option value="7">Tuần qua</option>
                            <option value="30">Tháng qua</option>
                            <option value="365">Năm qua</option>
                            <option value="all">Từ bài viết đầu tiên</option>
                        </select>
                    </div>

                    <canvas id="recentActivityChart" style="max-width: 100%;"></canvas> <!-- Biểu đồ hoạt động gần đây -->
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <h5 class="card-title">Tỷ lệ bài viết</h5>
                    </div>
                    <canvas id="postStatusChart" style="max-width: 100%;"></canvas> <!-- Biểu đồ trạng thái -->
                </div>
            </div>
        </div>
    </div>

    <!-- Phần dành cho người dùng đang hoạt động -->
    <h3 class="mt-5">Người dùng hoạt động nhiều nhất</h3>
    <div class="list-group mb-4 shadow-sm">
        @foreach ($mostActiveUsers as $user)
        <a href="{{ route('admin.users.index', $user->id) }}"
            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <span><strong>{{ $user->username }}</strong> - Bài viết: {{ $user->posts_count }} | Bình luận: {{ $user->comments_count }}</span>
            <span class="badge bg-primary rounded-pill">{{ $user->posts_count }}</span>
        </a>
        @endforeach
    </div>

    <!-- Phần dành cho nhóm -->
    <h3 class="mt-5">Tổng số nhóm</h3>
    <p class="lead">
        <a href="{{ route('admin.groups.index') }}" class="text-decoration-none text-primary">
            <strong>{{ $totalGroups }}</strong> nhóm được tạo ra
        </a>
    </p>

    <!-- Phần dành cho các danh mục hàng đầu -->
    <h3 class="mt-5">Danh mục có nhiều bài viết nhất</h3>
    <div class="list-group mb-4 shadow-sm">
        @foreach ($topCategories as $category)
        <a href="{{ route('admin.categories.index', $category->id) }}"
            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <strong>{{ $category->name }}</strong>
            <span class="badge bg-info rounded-pill">{{ $category->posts_count }} bài viết</span>
        </a>
        @endforeach
    </div>
</div>

@endsection