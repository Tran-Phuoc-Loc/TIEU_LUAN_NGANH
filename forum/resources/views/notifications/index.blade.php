@extends('layouts.users')

@section('content')
<div class="row">
    <div class="post-container">
        <h1>Thông báo</h1>

        <!-- Nút đánh dấu tất cả là đã đọc -->
        <a href="{{ route('notifications.markAllAsRead') }}" class="btn btn-primary">Đánh dấu tất cả là đã đọc</a>

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#unread" style="color: #000000;">Thông báo chưa đọc</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#read" style="color: #000000;">Thông báo đã đọc</a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Tab thông báo chưa đọc -->
            <div id="unread" class="tab-pane fade show active">
                @if($unreadNotifications->isEmpty())
                <p>Không có thông báo chưa đọc.</p>
                @else
                @foreach($unreadNotifications as $notification)
                <div class="alert alert-danger">
                    {{ $notification->data['message'] }}
                    <a href="{{ route('posts.show', $notification->data['post_id']) }}">Xem bài viết</a>
                </div>
                @endforeach
                @endif
            </div>

            <!-- Tab thông báo đã đọc -->
            <div id="read" class="tab-pane fade">
                @if($readNotifications->isEmpty())
                <p>Không có thông báo đã đọc.</p>
                @else
                @foreach($readNotifications as $notification)
                <div class="alert alert-info" style="background-color: transparent;">
                    <!-- Thêm kiểm tra để xem thông báo có hiển thị -->
                    <p>Thông báo ID: {{ $notification->id }}</p>
                    <p>Nội dung: {{ $notification->data['message'] }}</p>
                    <a href="{{ route('posts.show', $notification->data['post_id']) }}">Xem bài viết</a>
                </div>
                @endforeach

                <!-- Phân trang -->
                {{ $readNotifications->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection