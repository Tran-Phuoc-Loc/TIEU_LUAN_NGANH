@extends('layouts.users')

@section('title', 'Danh sách Nhóm')

@section('css')
<style>
    /* CSS riêng cho trang này */


    .group-list {
        background-color: #f1f1f1;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        max-height: 100%;
    }

    .group-list ul {
        list-style: none;
        padding-left: 0;
    }

    .group-list .list-group-item {
        border: none;
        padding: 15px 10px;
        margin-bottom: 10px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
    }

    .group-list .list-group-item:hover {
        background-color: #e9ecef;
    }

    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 20px;
        border-left: 1px solid #e2e2e2;
        background-color: #fafafa;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        width: 100%;
    }

    .chat-title {
        margin-bottom: 20px;
        font-weight: bold;
        font-size: 1.5em;
    }

    .chat-messages {
        flex: 1;
        max-height: 100%;
        overflow-y: auto;
        margin-bottom: 20px;
        padding: 10px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: relative;
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

    /* Tin nhắn "sent" sẽ được căn bên phải và lệch nhẹ sang trái */
    .chat-message.sent {
        background-color: #daf8cb;
        align-self: flex-end;
        margin-left: auto;
        transform: translateX(-10px);
        /* Di chuyển sang trái */
        border-bottom-right-radius: 0;
    }

    /* Tin nhắn "received" sẽ căn bên trái và lệch nhẹ sang phải */
    .chat-message.received {
        background-color: #f1f0f0;
        align-self: flex-start;
        transform: translateX(10px);
        /* Di chuyển sang phải */
        border-bottom-left-radius: 0;
    }

    .timestamp {
        font-size: 0.75em;
        color: #666;
        margin-top: 5px;
        display: block;
    }

    .chat-input {
        display: flex;
        align-items: center;
        padding: 10px;
        background-color: #fff;
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
    }

    .btn-primary {
        border-radius: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        border-color: #007bff;
    }
</style>
@endsection
@section('content')
<div class="chat-container ">
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