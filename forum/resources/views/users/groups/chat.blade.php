@extends('layouts.users')

@section('title', 'Danh sách Nhóm')

<style>
    /* CSS riêng cho trang này */

    body {
        background-color: #f8f9fa;
        /* Màu nền nhẹ cho toàn bộ trang */
        font-family: Arial, sans-serif;
    }

    .chat-container {
        padding: 20px;
        max-width: 1200px;
        /* Giới hạn chiều rộng tối đa cho trang */
        margin: auto;
        /* Căn giữa trang */
    }

    .group-list {
        background-color: #ffffff;
        /* Nền trắng cho danh sách nhóm */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        height: calc(100vh - 40px);
        /* Chiều cao tối đa cho danh sách nhóm */
        margin-right: 20px;
        /* Khoảng cách bên phải */
    }

    .group-list h3 {
        margin-bottom: 15px;
        /* Khoảng cách dưới tiêu đề */
    }

    .group-list ul {
        list-style: none;
        padding-left: 0;
    }

    .group-list .list-group-item {
        border: none;
        padding: 15px;
        margin-bottom: 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
        transition: background-color 0.3s ease;
        cursor: pointer;
        /* Con trỏ thay đổi khi hover */
    }

    .group-list .list-group-item:hover {
        background-color: #e9ecef;
        /* Màu nền khi hover */
    }

    .chat-area {
        display: flex;
        flex-direction: column;
        padding: 20px;
        border-radius: 10px;
        background-color: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .chat-title {
        margin-bottom: 20px;
        font-weight: bold;
        font-size: 1.5em;
        color: #343a40;
        /* Màu chữ tiêu đề */
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        margin-bottom: 20px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .chat-message {
        margin-bottom: 10px;
        padding: 10px 15px;
        border-radius: 18px;
        max-width: 75%;
        position: relative;
        word-wrap: break-word;
        font-size: 1rem;
    }

    #private-chat-messages {
        max-height: 500px;
        /* Chiều cao tối đa cho khu vực chat */
        overflow-y: auto;
        /* Cho phép cuộn dọc khi nội dung vượt quá chiều cao */
        padding-right: 15px;
        /* Giữ không gian cho thanh cuộn */
    }

    /* Tin nhắn "sent" sẽ được căn bên phải */
    .chat-message.sent {
        background-color: #daf8cb;
        /* Màu nền cho tin nhắn gửi */
        align-self: flex-end;
        margin-left: auto;
        /* Căn bên phải */
        border-bottom-right-radius: 0;
        /* Bo tròn góc dưới bên phải */
    }

    /* Tin nhắn "received" sẽ căn bên trái */
    .chat-message.received {
        background-color: #f1f0f0;
        /* Màu nền cho tin nhắn nhận */
        align-self: flex-start;
        border-bottom-left-radius: 0;
        /* Bo tròn góc dưới bên trái */
    }

    .timestamp {
        font-size: 0.75em;
        color: #666;
        margin-top: 5px;
        display: block;
        /* Hiển thị timestamp trên dòng mới */
    }

    .chat-input {
        display: flex;
        align-items: center;
        padding: 10px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .input-group {
        flex: 1;
        display: flex;
        align-items: center;
    }

    .form-control {
        border-radius: 20px;
        padding: 10px 15px;
        border: 1px solid #ddd;
        margin-right: 10px;
        transition: border-color 0.3s;
        /* Hiệu ứng khi focus */
    }

    .form-control:focus {
        border-color: #007bff;
        /* Màu viền khi focus */
        outline: none;
        /* Bỏ outline mặc định */
    }

    .btn-primary {
        border-radius: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        /* Màu nền nút */
        border-color: #007bff;
        transition: background-color 0.3s;
        /* Hiệu ứng khi hover */
    }

    .btn-primary:hover {
        background-color: #0056b3;
        /* Màu nền khi hover */
        border-color: #0056b3;
    }
</style>

@section('content')
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="chat-container">
        <div class="row">
            <!-- Danh sách nhóm bên trái -->
            <div class="col-md-3 group-list">
                <h3>Danh sách nhóm</h3>
                @if($userGroups->isEmpty())
                <p>Bạn cần tham gia nhóm để nhắn tin.</p>
                @else
                <ul class="list-group">
                    @foreach($userGroups as $userGroup)
                    <li class="list-group-item">
                        <a href="{{ route('groups.chat', ['group' => $userGroup->id]) }}">{{ $userGroup->name }}</a>
                    </li>
                    @endforeach
                </ul>
                @endif

                <!-- Danh sách bạn bè -->
                <h3>Danh sách bạn bè</h3>
                <ul class="list-group">
                    @foreach($friends as $friend)
                    <li class="list-group-item">
                        <a href="{{ route('chat.private.show', ['receiverId' => $friend->id, 'group' => $group->id ?? null]) }}">{{ $friend->username }}</a>
                    </li>
                    @endforeach
                </ul>

            </div>

            <!-- Khu vực chat bên phải -->
            <div class="col-md-8 chat-area">
                @if(isset($group))
                <h3 class="chat-title">Chat trong nhóm: {{ $group->name }}</h3>
                <div class="chat-messages" id="group-chat-messages">
                    @foreach ($group->chats as $chat)
                    <div class="chat-message @if($chat->user_id === Auth::id()) sent @else received @endif">
                        <strong>{{ $chat->user->username }}:</strong>
                        <p>{{ $chat->message }}</p>
                        <span class="timestamp">{{ $chat->created_at->diffForHumans() }}</span>
                    </div>
                    @endforeach
                </div>
                <!-- Form chat nhóm -->
                <form id="group-chat-form" class="chat-input" onsubmit="event.preventDefault(); sendMessage('{{ $group->id }}', true);">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="Nhập tin nhắn..." required>
                        <button type="button" class="btn btn-primary" onclick="sendMessage('{{ $group->id }}', true);">Gửi</button>
                    </div>
                </form>

                @elseif(isset($receiver))
                <h3 class="chat-title">Chat với: {{ $receiver->username }}</h3>
                <div class="chat-messages" id="private-chat-messages">
                    @foreach ($messages as $message)
                    <div class="chat-message @if($message->sender_id === Auth::id()) sent @else received @endif">
                        <strong>{{ $message->sender->username }}:</strong>
                        <p>{{ $message->content }}</p>
                        <span class="timestamp">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                    @endforeach
                </div>
                <!-- Form chat cá nhân -->
                <form id="private-chat-form" class="chat-input" onsubmit="event.preventDefault(); sendMessage('{{ $receiver->id }}');">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="Nhập tin nhắn..." required>
                        <button type="button" class="btn btn-primary" onclick="sendMessage('{{ $receiver->id }}');">Gửi</button>
                    </div>
                </form>
                @else
                <p>Chọn một nhóm hoặc một người bạn để bắt đầu nhắn tin.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Tự động cuộn xuống cuối khi có tin nhắn mới
        document.addEventListener("DOMContentLoaded", function() {
            const groupChatMessages = document.getElementById('group-chat-messages');
            const privateChatMessages = document.getElementById('private-chat-messages');

            if (groupChatMessages) {
                groupChatMessages.scrollTop = groupChatMessages.scrollHeight;
            }
            if (privateChatMessages) {
                privateChatMessages.scrollTop = privateChatMessages.scrollHeight;
            }
        });
    </script>

    @endsection