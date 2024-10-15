@extends('layouts.users')

@section('title', 'Chào Mừng Đến TechTalks')

@section('content')
<div class="row">
    <div class="post-container">
        <div class="welcome-content">
            <h1>Chào mừng bạn đến với <strong>TechTalks</strong> <br> Hãy tham gia cùng chúng tôi và bắt đầu thảo luận ngay hôm nay!</h1>
            <p>Trang chào mừng này là nơi bắt đầu cho hành trình của bạn trong cộng đồng <strong>TechTalks</strong>.</p>
            <p>Đừng bỏ lỡ cơ hội để tham gia cùng chúng tôi trong những cuộc thảo luận sôi động về công nghệ. Khám phá, chia sẻ và học hỏi ngay hôm nay!</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Bài viết mới</h5>
                <p class="card-text">Các cuộc thảo luận mới nhất trong diễn đàn.</p>
                <a href="{{ route('users.index') }}" class="btn btn-primary">Xem Bài viết</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Danh mục phổ biến</h5>
                <p class="card-text">Khám phá các danh mục phổ biến nhất trong diễn đàn.</p>
                <a href="{{ route('categories.index') }}" class="btn btn-primary">Xem Danh Mục</a>
            </div>
        </div>
    </div>
</div>
@endsection