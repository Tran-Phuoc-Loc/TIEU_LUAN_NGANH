@extends('layouts.users')

@section('content')
<style>
    .chat-messages {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .chat-message {
        display: flex;
        justify-content: flex-start;
        /* Đặt tin nhắn người bán bên trái */
    }

    .chat-message.sent {
        justify-content: flex-end;
        /* Đặt tin nhắn người mua sang bên phải */
    }

    .message-content {
        background-color: #f0f0f0;
        padding: 10px;
        border-radius: 10px;
        max-width: 70%;
        word-wrap: break-word;
    }

    .chat-message.sent .message-content {
        background-color: #daf8cb;
        /* Màu nền của tin nhắn người gửi (người mua) */
        text-align: right;
    }

    .chat-message.received .message-content {
        background-color: #f1f0f0;
        /* Màu nền của tin nhắn người nhận (người bán) */
    }

    .timestamp {
        font-size: 0.8em;
        color: #888;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <!-- Menu điều hướng cho màn hình lớn -->
        <div class="col-lg-2 col-md-1 sidebar d-none d-md-block" style="background-color: #fff; position: fixed; height: 100vh; overflow-y: auto;">
            <div class="vertical-navbar">
                <!-- Thông tin người dùng -->
                <div class="user-info text-center mb-4" style="background-color: black;background-image: linear-gradient(135deg, #52545f 0%, #383a45 50%);">
                    @if(auth()->check())
                    <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('storage/images/avataricon.png') }}"
                        alt="Profile picture of {{ auth()->user()->username }}"
                        class="rounded-circle" style="width: 45px; height: 50px;">
                    <h5 class="d-none d-md-block" style="color: #fff;">{{ auth()->user()->username }}</h5>
                    <hr style="border-top: 1px solid black; margin: 10px 0;">
                    @endif
                </div>

                <nav class="navbar navbar-dark flex-column">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">
                                <i class="fas fa-house"></i>
                                <span class="d-none d-lg-inline">Trang chủ</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="bi bi-pencil"></i>
                                <span class="d-none d-lg-inline">Bài viết của bạn</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">
                                <i class="bi bi-folder"></i>
                                <span class="d-none d-lg-inline">Danh mục</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forums.index') }}">
                                <i class="bi bi-chat-dots"></i>
                                <span class="d-none d-lg-inline">Diễn đàn</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <hr class="my-4">

                <nav class="navbar navbar-dark flex-column">
                    <ul class="navbar-nav">
                        <li class="nav-item" style="padding-bottom: 10px;">
                            <a href="{{ route('products.index') }}" class="btn btn-success">
                                <i class="fas fa-file-pen"></i>
                                <span class="d-none d-lg-inline">Tạo sản phẩm</span>
                            </a>
                        </li>
                        <li class="nav-item" style="padding-bottom: 10px;">
                            <a href="{{ route('users.groups.create') }}" class="btn btn-success">
                                <i class="bi bi-people"></i>
                                <span class="d-none d-lg-inline">Tạo nhóm</span>
                            </a>
                        </li>
                        <li class="nav-item" style="text-align: center;">
                            @if ($groups->isNotEmpty())
                            @php $firstGroup = $groups->first(); @endphp
                            <a href="{{ route('groups.chat', $firstGroup->id) }}">
                                <i class="fas fa-comment-sms" style="font-size: 40px"></i>
                            </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="col-lg-7 col-md-7 offset-lg-3 content-col shadow-lg rounded p-4" style="border: 2px solid #e0e0e0; background-color:#f9f9f9;">
            <!-- Hiển thị thông tin người nhận -->
            @if(isset($receiver))
            <h3 class="chat-title text-primary">
                @if(Auth::id() === $product->user_id) <!-- Nếu người đăng nhập là người bán -->
                Nhắn tin với người mua: {{ $receiver->username }}
                @else <!-- Nếu người đăng nhập là người mua -->
                Nhắn tin với người bán: {{ $receiver->username }}
                @endif
            </h3>

            <!-- Khu vực hiển thị tin nhắn -->
            <div class="chat-messages mb-4 p-3 rounded shadow-sm" id="product-chat-messages" style="height: 400px; overflow-y: auto; background-color: #fff;">
                @foreach ($messages as $message)
                <div class="chat-message @if($message->sender_id === Auth::id()) sent @else received @endif">
                    <div class="message-content">
                        <strong>{{ $message->sender->username }}:</strong>
                        <p>{{ $message->content }}</p>
                        <span class="timestamp">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Form gửi tin nhắn -->
            <form id="product-chat-form" class="chat-input mt-3" onsubmit="event.preventDefault(); sendMessages('{{ $product->id }}', '{{ $receiver->id }}');">
                @csrf
                <div class="input-group shadow-sm">
                    <input type="text" name="message" class="form-control rounded-start" placeholder="Nhập tin nhắn..." required>
                    <button type="button" class="btn btn-primary" onclick="sendMessages('{{ $product->id }}', '{{ $receiver->id }}');">Gửi</button>
                </div>
            </form>
            @else
            <p class="text-muted">Chọn một sản phẩm và người bán để bắt đầu nhắn tin.</p>
            @endif
        </div>
    </div>
</div>
<script>
    function sendMessages(productId, receiverId) {
        console.log("Gọi hàm sendMessage với productId:", productId, "và receiverId:", receiverId);
        const messageInput = document.querySelector('input[name="message"]');
        const message = messageInput.value.trim();

        if (message === '') return;

        // Gửi yêu cầu AJAX sử dụng URL từ route()
        const url = `{{ route('chat.product.send', ['productId' => ':productId', 'receiverId' => ':receiverId']) }}`
            .replace(':productId', productId)
            .replace(':receiverId', receiverId);

        console.log("URL:", url); // In URL ra để kiểm tra

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message
                })
            })
            .then(response => {
                // Log ra status và text response
                console.log("Response status:", response.status);
                return response.text(); // Chuyển đổi thành text để kiểm tra
            })
            .then(text => {
                console.log("Response text:", text); // In ra phản hồi
                return JSON.parse(text); // Chuyển đổi thành JSON
            })
            .then(data => {
                // Kiểm tra phản hồi từ server
                if (data.status === 'success') {
                    // Tạo phần tử mới cho tin nhắn
                    const newMessage = document.createElement('div');
                    newMessage.classList.add('chat-message', 'sent'); // Thêm class 'sent' cho tin nhắn của người gửi
                    newMessage.innerHTML = `
                <strong>${data.sender_username}:</strong>
                <p>${data.message}</p>
                <span class="timestamp">${data.timestamp}</span>
            `;

                    // Thêm tin nhắn vào khu vực hiển thị
                    document.getElementById('product-chat-messages').appendChild(newMessage);

                    // Cuộn đến cuối khu vực tin nhắn
                    const chatMessages = document.getElementById('product-chat-messages');
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                } else {
                    console.error('Lỗi khi gửi tin nhắn:', data);
                }
            })
            .catch(error => {
                console.error('Có lỗi trong quá trình gửi tin nhắn:', error);
            });

        // Làm sạch input sau khi gửi
        messageInput.value = '';
    }
</script>
@endsection