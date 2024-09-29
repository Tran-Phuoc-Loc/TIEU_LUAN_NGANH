@extends('layouts.users')

@section('title', 'Danh sách Nhóm')

@section('css')
<style>
    /* CSS riêng cho trang này */
    .chat-container {
    display: flex; /* Sử dụng flexbox để tạo bố cục */
    margin-top: 20px; /* Khoảng cách phía trên */
}

.group-list {
    background-color: #f8f9fa; /* Màu nền nhẹ cho danh sách nhóm */
    padding: 20px; /* Khoảng cách bên trong */
    border-radius: 5px; /* Bo góc */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ */
    overflow-y: auto; /* Cho phép cuộn dọc */
    max-height: 500px;
}

.chat-area {
    padding: 20px; /* Khoảng cách bên trong */
    border-left: 1px solid #dee2e6; /* Đường viền trái */
}

.chat-title {
    margin-bottom: 20px; /* Khoảng cách dưới tiêu đề chat */
}

.chat-messages {
    max-height: 400px; /* Giới hạn chiều cao */
    overflow-y: auto; /* Thêm cuộn dọc */
    margin-bottom: 20px; /* Khoảng cách bên dưới */
    padding: 10px; /* Khoảng cách bên trong */
    background-color: #ffffff; /* Màu nền trắng cho tin nhắn */
    border-radius: 5px; /* Bo góc */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ */
}

.chat-message {
    margin-bottom: 10px; /* Khoảng cách giữa các tin nhắn */
    padding: 8px; /* Khoảng cách bên trong */
    border-radius: 5px; /* Bo góc */
}

.chat-message.sent {
    background-color: #d1e7dd; /* Màu nền cho tin nhắn đã gửi */
    align-self: flex-end; /* Đẩy tin nhắn sang bên phải */
}

.chat-message.received {
    background-color: #f8d7da; /* Màu nền cho tin nhắn nhận được */
    align-self: flex-start; /* Đẩy tin nhắn sang bên trái */
}

.timestamp {
    font-size: 0.8em; /* Kích thước chữ nhỏ hơn */
    color: gray; /* Màu chữ xám */
    display: block; /* Hiển thị trên dòng mới */
}

.chat-input {
    display: flex; /* Hiện thị dạng flex */
}

.input-group {
    flex: 1; /* Chiếm toàn bộ không gian có sẵn */
}

.form-control {
    border-radius: 5px; /* Bo góc cho ô nhập */
    margin-right: 10px; /* Khoảng cách bên phải */
}
</style>
@endsection
@section('content')
<div class="container chat-container">
    <div class="row">
        <!-- Danh sách nhóm bên trái -->
        <div class="col-md-4 group-list">
            <h3>Danh sách nhóm</h3>
            <ul class="list-group">
                @foreach($userGroups as $userGroup)
                    <li class="list-group-item">
                        <a href="{{ route('groups.chat', $userGroup->id) }}">{{ $userGroup->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Khu vực chat bên phải -->
        <div class="col-md-8 chat-area">
            <h3 class="chat-title">Chat trong nhóm: {{ $group->name }}</h3>

            <div class="chat-messages mb-3">
                @foreach ($group->chats as $chat)
                    <div class="chat-message @if($chat->user_id === Auth::id()) sent @else received @endif">
                        <strong>{{ $chat->user->username }}:</strong>
                        <p>{{ $chat->message }}</p>
                        <span class="timestamp">{{ $chat->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('chats.store', $group->id) }}" method="POST" class="chat-input">
                @csrf
                <div class="input-group">
                    <input type="text" name="message" class="form-control" placeholder="Nhập tin nhắn..." required>
                    <button class="btn btn-primary" type="submit">Gửi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
