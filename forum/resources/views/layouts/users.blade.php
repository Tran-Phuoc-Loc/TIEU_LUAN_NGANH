<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Nơi chia sẻ và thảo luận về công nghệ.">
    <meta name="keywords" content="TechTalks, công nghệ, thảo luận">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="authenticated" content="true">
    @else
    <meta name="authenticated" content="false">
    @endauth
    <title>@yield('title', 'Chào Mừng Đến TeachTalks')</title>

    <!-- Bootstrap & Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- App CSS & JS (via Vite) -->
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <style>
        /* Container chính */
        .container {
            display: flex;
        }

        .post-card {
            display: flex;
            background-color: #fff;
            border: 1px solid #ddd;
            margin-bottom: 1rem;
            border-radius: 5px;
            padding: 10px;
            position: relative;
            padding-left: 10px;
            flex-direction: column;
        }

        .vote-section {
            width: 50px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            margin-left: 60px;
        }

        .vote-section i {
            font-size: 20px;
            cursor: pointer;
        }

        /* Các hành động của bài viết */
        .post-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        /* Mẫu bình luận */
        .comment-form {
            width: 100%;
        }

        .btn-link {
            padding: 0;
            vertical-align: baseline;
        }

        /* Nội dung bài viết */
        .post-content {
            flex-grow: 1;
        }

        .post-title {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .post-description {
            color: #555;
            margin-bottom: 10px;
        }

        /* Chân bài viết */
        .post-footer {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #888;
            flex-direction: column;
            position: relative;
            padding-top: 10px;
            /* Khoảng cách trên để tránh nội dung bị che phủ */
        }

        .post-footer::before {
            content: "";
            /* Tạo một phần tử giả */
            position: absolute;
            /* Đặt vị trí tuyệt đối */
            top: 0;
            /* Đặt ở đầu phần tử */
            left: 0;
            /* Bắt đầu từ bên trái */
            right: 0;
            /* Kéo dài đến bên phải */
            height: 2px;
            /* Độ dày của đường gạch */
            background-color: #ccc;
            /* Màu sắc của đường gạch */
        }

        /* Thông tin bài viết */
        .post-meta {
            font-size: 0.9rem;
            color: #888;
        }

        .post-category {
            white-space: nowrap;
            padding-top: 10px;
        }

        .d-flex {
            width: 100%;
            /* Đảm bảo phần tử chiếm đủ không gian */
        }

        .post-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .post-author {
            font-weight: bold;
        }

        .post-time {
            color: #555;
        }

        /* Hình ảnh bài viết */
        .post-image {
            margin: 10px 0;
            text-align: center;
            width: 100%;
            height: 300px;
            overflow: hidden;
            /* Đảm bảo kích thước không vượt quá phần tử chứa */
            position: relative;
        }

        .post-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Đảm bảo ảnh nằm gọn trong khung mà không bị biến dạng */
            object-position: center;
            /* Căn giữa ảnh */
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Thanh điều hướng dọc */
        .vertical-navbar {
            height: calc(100vh - 56px);
            /* Điều chỉnh để chiều cao của thanh điều hướng không vượt quá chiều cao của viewport */
            z-index: 1000;
            /* Đảm bảo phần tử luôn nằm trên các phần tử khác */
            overflow-y: auto;
            /* Cho phép thanh cuộn dọc xuất hiện nếu nội dung bên trong vượt quá chiều cao phần tử */
            flex-shrink: 0;
            /* Ngăn không cho phần tử bị co lại trong mô hình Flexbox, đảm bảo kích thước của nó không bị thay đổi */
        }

        /* Nội dung chào mừng */
        .welcome-content {
            padding: 20px;
            flex: 1;
            position: relative;
        }

        /* Khoảng cách cho nút điều hướng */
        .navbar-toggler {
            margin-right: 10px;
            /* Đảm bảo khoảng cách hợp lý */
        }

        /* Thanh điều hướng cố định ở dưới cùng */
        .fixed-bottom {
            background-color: #333;
            /* Màu nền cho thanh điều hướng */
        }

        .fixed-bottom .d-flex {
            justify-content: space-around;
            /* Căn giữa các mục */
            width: 100%;
            /* Chiếm toàn bộ chiều rộng */
        }

        .fixed-bottom .nav-link {
            color: #ffffff;
            /* Màu chữ sáng hơn */
            font-size: 1rem;
            /* Kích thước chữ */
            padding: 10px 0;
            /* Khoảng cách trên và dưới */
            transition: background-color 0.3s;
            /* Hiệu ứng chuyển màu nền */
        }

        /* Hiệu ứng hover cho các liên kết */
        .fixed-bottom .nav-link:hover {
            background-color: #495057;
            /* Màu nền khi hover */
            border-radius: 5px;
            /* Bo tròn góc */
        }

        /* Vòng tròn bao quanh icon */
        .circle-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            /* Đường kính vòng tròn */
            height: 40px;
            /* Đường kính vòng tròn */
            background-color: #ff4081;
            /* Màu nền vòng tròn */
            border-radius: 50%;
            /* Tạo hình tròn */
            color: #fff;
            /* Màu chữ (icon) */
            transition: background-color 0.3s;
            /* Hiệu ứng chuyển màu nền */
        }

        /* Hiệu ứng hover cho vòng tròn */
        .btn-light:hover .circle-icon {
            background-color: #e91e63;
            /* Màu nền khi hover */
        }

        .comment-actions {
            display: flex;
            /* Sử dụng Flexbox để sắp xếp các nút */
            align-items: center;
            /* Căn giữa theo chiều dọc */
            margin-top: 10px;
            /* Khoảng cách giữa bình luận và các nút */
        }

        .comment-actions button {
            margin-right: 10px;
            /* Khoảng cách giữa các nút */
        }

        .comment-actions .likes-count {
            margin-left: 5px;
            /* Khoảng cách giữa nút và số lượng thích */
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            /* Sắp xếp theo chiều dọc */
            height: 100%;
            /* Chiều cao tối đa */
            overflow-y: auto;
            /* Kích hoạt cuộn dọc */
        }

        .textarea-container {
            margin-top: auto;
            /* Đẩy form xuống dưới cùng */
        }

        .comments-list {
            flex: 1;
            /* Chiếm không gian còn lại */
            margin-bottom: 15px;
            /* Khoảng cách giữa danh sách bình luận và form */
        }

        .comment {
            margin-bottom: 10px;
            /* Khoảng cách giữa các bình luận */
            border-bottom: 1px solid #e0e0e0;
            /* Đường phân cách giữa các bình luận */
            padding-bottom: 10px;
            /* Khoảng cách bên dưới bình luận */
        }


        .dropdown-toggle::after {
            display: none;
            /* Ẩn mũi tên */
        }

        .dropdown {
            position: relative;
            /* Để menu được định vị chính xác */
        }

        .dropdown-toggle {
            background-color: transparent;
            /* Không có màu nền */
            border: none;
            /* Bỏ viền */
            color: inherit;
            /* Giữ lại màu chữ mặc định */
        }

        .dropdown-menu {
            border: none;
            /* Bỏ viền cho menu */
        }

        .dropdown-item:hover {
            background-color: rgba(0, 0, 0, 0.1);
            /* Màu nền khi hover */
        }

        .like-count {
            display: inline;
            /* Đảm bảo phần tử hiển thị */
        }

        .new-notification {
            color: #d9534f;
            /* Màu đỏ nhạt cho thông báo khi có thông báo mới */
            font-weight: bold;
            /* Tô đậm khi có thông báo mới */
        }

        .modal-backdrop {
            z-index: auto;
        }

        .replies {
            margin-left: 40px;
            /* Thụt lề cho bình luận trả lời */
            border-left: 2px solid #f0f0f0;
            /* Đường viền bên trái cho phần trả lời */
            padding-left: 10px;
        }

        .btn-edit-product {
            background-color: #ff9800;
            /* Màu cam, bạn có thể thay đổi màu này */
            border-color: #ff9800;
            color: white;
        }

        .btn-edit-product:hover {
            background-color: #fb8c00;
            /* Màu cam đậm khi hover */
            border-color: #fb8c00;
        }
    </style>
</head>

<body>
    <header>
        <!-- Navbar chính -->
        <nav class="navbar navbar-expand-lg navbar-dark" style="border-bottom: 1px solid #ddd; background-color:#fff">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('storage/images/bookicon.png') }}" alt="Description" loading="lazy">TechTalks
            </a>

            <!-- Thanh tìm kiếm -->
            <div class="search-bar mt-3">
                <form class="input-group" action="{{ url('users/posts') }}" method="GET">
                    <input class="form-control" type="search" name="query" placeholder="Tìm kiếm bài viết" aria-label="Search" value="{{ request('query') }}">
                    <button class="btn btn-outline-success" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
            </div>

            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <!-- Nút toggle cho màn hình nhỏ -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Các mục menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto" style="margin-top: 20px;">

                    <!-- Mục Shop -->
                    <li class="nav-item" style="padding-top: 7px;">
                        <a href="{{ route('products.index') }}" class="dropdown-item">
                            <i class="bi bi-bag"></i> Shop
                        </a>
                    </li>

                    <!-- Thông báo -->
                    @auth
                    <li class="nav-item dropdown" style="padding-top: 7px;">
                        <a href="{{ route('notifications.index') }}"
                            class="dropdown-item {{ auth()->check() && auth()->user()->unreadNotifications->count() > 0 ? 'new-notification' : '' }}">
                            <i class="fas fa-bell"></i>
                            @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="badge" style="color: #d9534f;">{{ auth()->user()->unreadNotifications->count() }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- Mục Profile -->
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-circle">
                                @if(Auth::user()->profile_picture)
                                <!-- Nếu có profile_picture, kiểm tra xem đó là URL tuyệt đối hoặc đường dẫn tĩnh -->
                                @if(filter_var(Auth::user()->profile_picture, FILTER_VALIDATE_URL))
                                <!-- Nếu profile_picture là URL, hiển thị trực tiếp -->
                                <img src="{{ Auth::user()->profile_picture }}" alt="Ảnh đại diện" class="img-fluid" style="border-radius: 50%;" loading="lazy">
                                @else
                                <!-- Nếu không phải URL (ví dụ, đường dẫn trong storage), thì tải ảnh từ thư mục public -->
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Ảnh đại diện" class="img-fluid" style="border-radius: 50%;" loading="lazy">
                                @endif
                                @else
                                <!-- Nếu không có ảnh, hiển thị ảnh mặc định hoặc ký tự đầu tiên của tên -->
                                <img src="{{ asset('storage/images/avataricon.png') }}" alt="Ảnh đại diện mặc định" class="img-fluid" style="border-radius: 50%;" loading="lazy">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @endif
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">{{ Auth::user()->name }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('users.profile.index', Auth::user()->id) }}">Thông tin cá nhân</a></li>
                            <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng Xuất</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item ms-3" style="text-align: right;">
                        <a class="nav-link" href="{{ route('login') }}">Đăng Nhập</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </nav>
    </header>


    <main class="main">
        <!-- Nội dung chính -->
        @yield('content')
    </main>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    // Xử lý chia sẻ lên Facebook
    $('.share-facebook').on('click', function(event) {
        event.preventDefault();
        var url = $(this).data('url');
        var facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
        window.open(facebookUrl, 'facebook-share-dialog', 'width=626,height=436');
    });

    // Xử lý chia sẻ lên Twitter
    $('.share-twitter').on('click', function(event) {
        event.preventDefault();
        var url = $(this).data('url');
        var twitterUrl = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(url) + '&text=Check out this post!';
        window.open(twitterUrl, 'twitter-share-dialog', 'width=626,height=436');
    });

    // Xử lý chia sẻ lên LinkedIn
    $('.share-linkedin').on('click', function(event) {
        event.preventDefault();
        var url = $(this).data('url');
        var linkedinUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url);
        window.open(linkedinUrl, 'linkedin-share-dialog', 'width=626,height=436');
    });

    const currentUserId = '{{ Auth::id() }}';

    // Hàm chung cho gọi API
    function sendApiRequest(url, method = 'GET', bodyData = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        };

        if (bodyData) {
            options.body = JSON.stringify(bodyData);
        }

        return fetch(url, options)
            .then(response => {
                if (response.redirected) {
                    // Nếu có chuyển hướng, có thể xử lý ở đây
                    window.location.href = response.url; // Điều hướng đến URL mới
                    return; // Dừng thực hiện tiếp
                }
                return response.json();
            })
            .catch(error => console.error('Error:', error));
    }

    // Hàm gửi tin nhắn
    function sendMessage(id, isGroup = false) {
        const messageInput = document.querySelector('input[name="message"]');
        const message = messageInput.value;

        if (!message.trim()) return;

        const url = isGroup ? `/users/group/${id}/chat` : `/chat/private/${id}/store`;

        sendApiRequest(url, 'POST', {
                message
            })
            .then(data => {
                updateMessages(data, isGroup);
                messageInput.value = ''; // Xóa nội dung ô input sau khi gửi
            });
    }

    // Hàm cập nhật giao diện với tin nhắn mới
    function updateMessages(data, isGroup = false) {
        const messageContainer = document.getElementById(isGroup ? 'group-chat-messages' : 'private-chat-messages');
        const messageClass = data.sender.id === currentUserId ? 'sent' : 'received';

        // Cập nhật nội dung tin nhắn
        const newMessageHTML = `
            <div class="chat-message ${data.sender.id === currentUserId ? 'sent' : 'received'}">
                <strong>${data.sender.username}:</strong>
                <p>${data.content}</p>
                <span class="timestamp">${new Date(data.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
            </div>
        `;

        messageContainer.insertAdjacentHTML('beforeend', newMessageHTML);
        scrollToBottom(messageContainer);
    }

    // Hàm tải lại danh sách tin nhắn
    function reloadMessages(id, isGroup) {
        const url = isGroup ? `/users/group/${id}/chat` : `/chat/private/${id}`;

        sendApiRequest(url)
            .then(data => {
                const messageContainer = document.getElementById(isGroup ? 'group-chat-messages' : 'private-chat-messages');
                messageContainer.innerHTML = ''; // Xóa nội dung cũ

                const messagesHTML = data.messages.map(message => {
                    const messageClass = message.sender_id === currentUserId ? 'sent' : 'received';
                    return `
                    <div class="chat-message ${messageClass}">
                        <strong>${message.sender.username}:</strong>
                        <p>${message.content}</p>
                        <span class="timestamp">${message.created_at}</span>
                    </div>
                `;
                }).join('');

                messageContainer.innerHTML = messagesHTML;
                scrollToBottom(messageContainer);
            });
    }

    // Hàm cuộn xuống cuối chat
    function scrollToBottom(container) {
        container.scrollTop = container.scrollHeight;
    }


    // Định nghĩa hàm redirectToLogin
    function redirectToLogin() {
        window.location.href = '/login'; // Chuyển hướng đến trang đăng nhập
    }

    $(document).ready(function() {

        // Định nghĩa parentId như một biến toàn cục
        let parentId = null;

        // Khởi tạo các biến DOM elements được sử dụng
        const commentModal = $('#commentModal'); // Modal để hiển thị bình luận
        const commentForm = $('#commentForm'); // Form để gửi bình luận
        const closeButton = $('.close'); // Nút đóng modal
        const commentsList = $('.comments-list'); // Danh sách bình luận trong modal
        const loginModal = $('#loginModal'); // Modal đăng nhập

        /**
         * Hàm tạo HTML cho một bình luận
         * @param {Object} comment - Đối tượng chứa thông tin bình luận
         * @param {number} postId - ID của bài viết
         * @returns {string} HTML string của bình luận
         */
        function createCommentHtml(comment, postId, depth = 0, maxDepth = 2) {
            // Xử lý thời gian bình luận được tạo bằng thư viện moment.js
            const createdAt = moment(comment.created_at).fromNow();
            // Đặt giá trị mặc định cho lượt thích là 0 nếu chưa có
            const likesCount = comment.likes_count || 0;
            // Hiển thị tên người được trả lời nếu đây là reply
            const repliedToUser = comment.replied_to_user ? `Trả lời ${comment.replied_to_user.username}` : '';
            // Lấy URL avatar của người dùng, nếu không có thì dùng avatar mặc định
            const avatarUrl = comment.user.profile_picture ? (comment.user.profile_picture.startsWith('http') ? comment.user.profile_picture : `/storage/${comment.user.profile_picture}`) : '/storage/images/avataricon.png';


            // Hiển thị thông báo nếu bình luận đã bị xóa
            const commentContent = comment.is_deleted ? '<i>Bình luận này đã bị xóa.</i>' : comment.content;
            // console.log(comment.is_owner);
            // Khởi tạo nút xóa (chỉ hiển thị cho chủ bình luận)
            let deleteButton = '';
            if (comment.is_owner) {
                deleteButton = `<button class="delete-button" data-comment-id="${comment.id}">
                <i class="fas fa-trash-alt"></i> Xóa
            </button>`;
            }

            // Tạo HTML cho bình luận
            let commentHTML = `
                <div class="comment p-3 mb-3 border rounded shadow-sm" id="comment-${comment.id}" style="background-color: #f9f9f9;">
                    <div class="d-flex align-items-center mb-2">
                        <img src="${avatarUrl}" alt="Avatar" class="rounded-circle me-3" width="40" height="40" loading="lazy" style="border: 1px solid #ddd;">
                        <div>
                            <strong>${comment.user.username}</strong>
                            <small class="text-muted"> • ${createdAt}</small>
                        </div>
                    </div>
                    <div class="ms-4">
                        ${repliedToUser ? `<div class="text-muted small mb-1">${repliedToUser}</div>` : ''}
                        <p class="mb-2" style="font-size: 1rem; line-height: 1.5;">${commentContent}</p>
                        ${comment.image_url ? `<div class="comment-image mb-2"><img src="/storage/${comment.image_url}" class="img-fluid rounded" alt="Comment Image" loading="lazy" style="max-width: 100%; height: auto;"></div>` : ''}
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex">
                                <button class="btn btn-sm btn-outline-secondary like-button me-2" data-comment-id="${comment.id}" style="border-radius: 20px; padding: 5px 10px;">
                                    <i class="far fa-thumbs-up"></i> <span class="like-count">${likesCount}</span>
                                </button>
                                <button class="btn btn-sm btn-outline-primary reply-button" data-comment-id="${comment.id}" style="border-radius: 20px; padding: 5px 10px;">
                                    <i class="fas fa-reply"></i> Trả lời
                                </button>
                            </div>
                            ${deleteButton ? `
                                <button class="btn btn-sm btn-outline-danger delete-button" data-comment-id="${comment.id}" data-post-id="${postId}" style="border-radius: 20px; padding: 5px 10px;">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>` : ''}
                        </div>
                    </div>
                    <!-- Container cho các replies -->
                    <div class="replies ms-4 mt-3" id="replies-${comment.id}"></div>
                </div>
            `;

            // Nếu có replies và chưa vượt quá độ sâu tối đa, hiển thị các replies
            if (comment.replies && comment.replies.length > 0 && depth < maxDepth) {
                const repliesHTML = comment.replies
                    .map(reply => createCommentHtml(reply, postId, depth + 1, maxDepth))
                    .join('');
                commentHTML = commentHTML.replace(`<div class="replies ms-4 mt-3" id="replies-${comment.id}"></div>`, `<div class="replies ms-4 mt-3" id="replies-${comment.id}">${repliesHTML}</div>`);
            }

            return commentHTML;
        }

        // Hàm để hiển thị toàn bộ cây bình luận
        function renderComments(comments, postId) {
            const commentsContainer = document.getElementById('comments-container');
            commentsContainer.innerHTML = comments.map(comment => createCommentHtml(comment, postId)).join('');
        }

        /**
         * Hàm đệ quy xử lý và hiển thị các replies của bình luận
         * @param {Array} replies - Mảng chứa các reply
         * @param {number} parentId - ID của bình luận gốc
         * @param {number} postId - ID của bài viết
         */
        function handleReplies(replies, parentId, postId, level = 0) {
            const repliesContainer = $(`#replies-${parentId}`);

            // Kiểm tra nếu repliesContainer tồn tại
            if (!repliesContainer.length) {
                console.error('Không tìm thấy container để thêm replies cho comment với ID:', parentId);
                return;
            }

            // Giới hạn số lượng replies con tối đa là 2
            const maxReplies = 2;

            // Chỉ lấy tối đa 2 replies cho mỗi bình luận cha
            const limitedReplies = replies.slice(0, maxReplies);

            limitedReplies.forEach(reply => {
                // Kiểm tra nếu reply là một bình luận con đúng với parentId
                if (reply.parent_id === parentId) {
                    const replyHtml = createCommentHtml(reply, postId);
                    repliesContainer.append(replyHtml);

                    // Nếu reply có thêm replies, gọi đệ quy để xử lý
                    if (reply.replies && reply.replies.length > 0 && level < maxReplies) {
                        handleReplies(reply.replies, reply.id, postId, level + 1);
                    }
                }
            });
        }

        // Xử lý sự kiện khi người dùng nhấn vào nút bình luận
        $('.comment-toggle').on('click', function(e) {
            e.preventDefault(); // Ngừng reload trang
            const postId = $(this).data('post-id');
            const postTitle = $(this).closest('.post-card').find('.post-title').text();

            $('#modalPostTitle').text(postTitle);
            commentModal.show();

            $('#parent_id').val(0); // Reset parent_id khi mở modal mới 
            $('#parent_id').val(null); // Đặt parent_id là null nếu không có comment cha

            // Lấy dữ liệu bình luận
            $.get(`users/posts/${postId}/comments`, function(data) {
                commentsList.empty(); // Xóa các bình luận cũ

                if (data.comments && data.comments.length > 0) {
                    data.comments.forEach(comment => {
                        const commentHtml = createCommentHtml(comment, postId);
                        commentsList.append(commentHtml);

                        if (comment.replies && comment.replies.length > 0) {
                            handleReplies(comment.replies, comment.id, postId);
                        }
                    });
                } else {
                    commentsList.append('<p>Chưa có bình luận nào.</p>');
                }
                commentForm.attr('action', `users/posts/${postId}/comments`); // Cập nhật thuộc tính action của form
                return false; // Ngừng reload trang
            });
        });

        // Xử lý sự kiện khi người dùng gửi bình luận
        $('#commentForm').on('submit', function(e) {
            e.preventDefault();

            const postId = $(this).data('post-id'); // Lấy postId từ data attribute
            console.log('Post ID:', postId); // Log postId để kiểm tra

            if (!postId) {
                alert('Post ID không hợp lệ!'); // Thông báo nếu postId không có
                return; // Dừng thực hiện nếu không có postId
            }

            const formData = new FormData(this);

            $.ajax({
                url: `/users/posts/${postId}/comments`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        // Lấy bình luận mới từ phản hồi
                        const comment = data.comment; // Định nghĩa comment ở đây

                        // Xử lý thời gian bình luận được tạo
                        const createdAt = moment(comment.created_at).fromNow();

                        // Đặt giá trị mặc định cho lượt thích
                        const likesCount = comment.likes_count || 0;

                        // Hiển thị tên người được trả lời nếu có
                        const repliedToUser = comment.replied_to_user ? `Trả lời ${comment.replied_to_user.username}` : '';

                        // Lấy URL avatar của người dùng
                        const avatarUrl = comment.user.profile_picture ?
                            (comment.user.profile_picture.startsWith('http') ? comment.user.profile_picture : `/storage/${comment.user.profile_picture}`) :
                            '/storage/images/avataricon.png';

                        // Hiển thị nội dung bình luận
                        const commentContent = comment.is_deleted ? '<i>Bình luận này đã bị xóa.</i>' : comment.content;

                        // Tạo HTML cho bình luận mới
                        const newCommentHtml = `
                        <div class="comment p-3 mb-3 border rounded shadow-sm" id="comment-${comment.id}" style="background-color: #f9f9f9;">
                            <div class="d-flex align-items-center mb-2">
                                <img src="${avatarUrl}" alt="Avatar" class="rounded-circle me-3" width="40" height="40" loading="lazy" style="border: 1px solid #ddd;">
                                <div>
                                    <strong>${comment.user.username}</strong>
                                    <small class="text-muted"> • ${createdAt}</small>
                                </div>
                            </div>
                            <div class="ms-4">
                                ${repliedToUser ? `<div class="text-muted small mb-1">${repliedToUser}</div>` : ''}
                                <p class="mb-2" style="font-size: 1rem; line-height: 1.5;">${commentContent}</p>
                                ${comment.image_url ? `<div class="comment-image mb-2"><img src="/storage/${comment.image_url}" class="img-fluid rounded" alt="Comment Image" loading="lazy" style="max-width: 100%; height: auto;"></div>` : ''}
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex">
                                        <button class="btn btn-sm btn-outline-secondary like-button me-2" data-comment-id="${comment.id}" style="border-radius: 20px; padding: 5px 10px;">
                                            <i class="far fa-thumbs-up"></i> <span class="like-count">${likesCount}</span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary reply-button" data-comment-id="${comment.id}" style="border-radius: 20px; padding: 5px 10px;">
                                            <i class="fas fa-reply"></i> Trả lời
                                        </button>
                                    </div>
                                    ${comment.is_owner ? `
                                        <button class="btn btn-sm btn-outline-danger delete-button" data-comment-id="${comment.id}" data-post-id="${postId}" style="border-radius: 20px; padding: 5px 10px;">
                                            <i class="fas fa-trash-alt"></i> Xóa
                                        </button>` : ''}
                                </div>
                            </div>
                            <div class="replies ms-4 mt-3" id="replies-${comment.id}"></div>
                        </div>
                    `;

                        // Thêm bình luận vào danh sách
                        $('.comments-list').prepend(newCommentHtml);

                        // Reset form
                        $('#commentForm')[0].reset();
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Có lỗi xảy ra';
                    console.error('Lỗi:', errorMessage);
                    alert(errorMessage);
                }
            });

            return false; // Chặn reload
        });

        $(document).on('click', '.reply-button', function() {
            const commentId = $(this).data('comment-id');
            const commentUserName = $(this).closest('.comment').find('strong').first().text();

            parentId = commentId; // Đặt parentId là ID của bình luận mà người dùng đang trả lời

            $('#parent_id').val(parentId); // Đặt parent_id cho comment cha

            // Đưa con trỏ vào ô textarea và focus
            const textarea = $('textarea[name="content"]');
            textarea.val(`@${commentUserName} `).focus();

            // Hiển thị thông báo đang trả lời
            $('#replyToUser').text(`Đang trả lời bình luận của ${commentUserName}:`).show();
        });

        // Xử lý sự kiện xóa bình luận
        $(document).on('click', '.delete-button', function() {
            const commentId = parseInt($(this).data('comment-id'));
            const postId = $(this).data('post-id');

            // console.log("Comment ID:", commentId);
            // console.log("Post ID:", postId);

            if (confirm('Bạn có chắc muốn xóa bình luận này không?')) {
                $.ajax({
                    url: '/users/posts/' + postId + '/comments/' + commentId,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // console.log(response);
                        if (response.success) {
                            // Lấy element của bình luận
                            const $commentElement = $(`#comment-${commentId}`);

                            // Kiểm tra xem đây có phải là bình luận cha không
                            // Bình luận cha sẽ nằm trực tiếp trong .comments-list
                            const isParentComment = $commentElement.parent().hasClass('comments-list');

                            if (isParentComment) {
                                // Nếu là bình luận cha, xóa tất cả bình luận con
                                // Tìm và xóa tất cả các replies trong container của bình luận này
                                $commentElement.find('.replies').find('.comment').each(function() {
                                    $(this).slideUp(400, function() {
                                        $(this).remove();
                                    });
                                });
                                // Sau đó xóa bình luận cha
                                $commentElement.slideUp(400, function() {
                                    $(this).remove();
                                });

                                // Kiểm tra nếu không còn bình luận nào
                                if ($('.comments-list .comment').length === 0) {
                                    $('.comments-list').append('<p>Chưa có bình luận nào.</p>');
                                }
                            } else {
                                // Nếu là bình luận con, chỉ xóa bình luận đó
                                $commentElement.slideUp(400, function() {
                                    $(this).remove();
                                });
                            }

                            // Hiển thị thông báo thành công
                            const successMessage = isParentComment ?
                                'Bình luận và tất cả phản hồi đã được xóa.' :
                                'Bình luận đã được xóa.';

                            // Sử dụng toast hoặc alert để thông báo
                            if (typeof Toastify === 'function') {
                                Toastify({
                                    text: successMessage,
                                    duration: 3000,
                                    gravity: "top",
                                    position: 'right',
                                    backgroundColor: "#4CAF50"
                                }).showToast();
                            } else {
                                alert(successMessage);
                            }
                        } else {
                            // Xử lý khi xóa không thành công
                            const errorMessage = response.message || 'Có lỗi xảy ra khi xóa bình luận.';
                            if (typeof Toastify === 'function') {
                                Toastify({
                                    text: errorMessage,
                                    duration: 3000,
                                    gravity: "top",
                                    position: 'right',
                                    backgroundColor: "#f44336"
                                }).showToast();
                            } else {
                                alert(errorMessage);
                            }
                        }
                    },
                    error: function(jqXHR) {
                        console.error('Error:', jqXHR.responseText);
                        const errorMessage = 'Có lỗi xảy ra. Vui lòng thử lại.';
                        if (typeof Toastify === 'function') {
                            Toastify({
                                text: errorMessage,
                                duration: 3000,
                                gravity: "top",
                                position: 'right',
                                backgroundColor: "#f44336"
                            }).showToast();
                        } else {
                            alert(errorMessage);
                        }
                    }
                });
            }
        });

        // Đóng modal khi nhấn nút đóng
        closeButton.on('click', function() {
            commentModal.hide();
            $('#parent_id').val(0); // Reset parent_id khi đóng modal
        });

        // Đóng modal khi nhấn ra ngoài modal
        $(window).on('click', function(event) {
            if ($(event.target).is(commentModal)) {
                commentModal.hide();
                $('#parent_id').val(0); // Reset parent_id khi đóng modal
            }
        });

        // Đóng modal đăng nhập
        $('.close').on('click', function() {
            loginModal.hide();
        });

        $(window).on('click', function(event) {
            if ($(event.target).is(loginModal)) {
                loginModal.hide();
            }
        });

    });
    // Xử lý sự kiện nhấn nút like
    $(document).on('click', '.like-button', function(e) {
        e.preventDefault(); // Ngăn chặn hành động mặc định của link

        const button = $(this);
        const commentId = button.data('comment-id'); // Lấy ID bình luận
        const postId = button.data('post-id'); // Lấy ID bài viết
        const groupId = button.data('group-id'); // Lấy ID nhóm
        const likeCountElement = button.find('.like-count');

        // Gọi API để kiểm tra đăng nhập
        $.ajax({
            url: '/check-login', // Route để kiểm tra đăng nhập
            method: 'GET',
            success: function(data) {
                if (!data.isLoggedIn) {
                    // Hiển thị modal nếu người dùng chưa đăng nhập
                    $('#modalTitle').text('Đăng Nhập');
                    $('#modalMessage').text('Vui lòng đăng nhập để thích.');
                    loginModal.show(); // Hiển thị modal
                    return;
                }

                // Thực hiện yêu cầu thích
                let likeUrl;
                if (commentId) {
                    likeUrl = `/comments/${commentId}/like`; // URL cho like comment
                } else {
                    // likeUrl = `users/posts/${postId}/like`; // URL cho like bài viết
                    likeUrl = groupId ? `users/groups/${groupId}/posts/${postId}/like` : `users/posts/${postId}/like`;
                }
                $.ajax({
                    url: likeUrl,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        // console.log('Phản hồi từ máy chủ:', data); // Xem phản hồi đầy đủ
                        if (data.success) {
                            // Cập nhật giao diện
                            button.toggleClass('liked'); // Thay đổi class để hiển thị trạng thái

                            // Cập nhật số lượng like
                            likeCountElement.text(data.new_like_count); // Sử dụng giá trị từ phản hồi
                        } else {
                            alert('Đã có lỗi xảy ra. Vui lòng thử lại.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Có lỗi xảy ra khi thực hiện yêu cầu like:', xhr);
                        alert('Có lỗi xảy ra. Vui lòng thử lại.');
                    }
                });
            },
            error: function(xhr) {
                console.error('Có lỗi xảy ra khi kiểm tra đăng nhập:', xhr);
                alert('Có lỗi xảy ra khi kiểm tra đăng nhập.');
            }
        });
    });

    $(document).ready(function() {
        $(document).on('click', '.report-button', function(e) {
            e.preventDefault();

            const postId = $(this).data('post-id');

            // Kiểm tra trạng thái đăng nhập
            $.ajax({
                url: '/check-login', // Route để kiểm tra đăng nhập
                method: 'GET',
                success: function(data) {
                    if (!data.isLoggedIn) {
                        // Nếu chưa đăng nhập, hiển thị cảnh báo
                        alert('Bạn cần đăng nhập để báo cáo bài viết.');
                        redirectToLogin(); // Chuyển hướng đến trang đăng nhập
                        return;
                    }

                    // Hiển thị hộp thoại xác nhận
                    const reason = prompt("Nhập lý do báo cáo:");
                    if (!reason) {
                        alert('Bạn cần nhập lý do để báo cáo.')
                        return; // Nếu không xác nhận, thoát hàm
                    }

                    // Gán lý do vào input ẩn
                    $('#reasonInput-' + postId).val(reason);

                    // Gửi yêu cầu AJAX để báo cáo
                    $.ajax({
                        url: '/admin/reports/store',
                        method: 'POST',
                        data: {
                            post_id: postId,
                            reason: reason, // Sử dụng lý do người dùng nhập
                            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                        },
                        success: function(response) {
                            alert('Bài viết đã được báo cáo.');
                            // Có thể làm mới trang hoặc cập nhật giao diện người dùng
                        },
                        error: function(xhr) {
                            console.error(xhr);
                            alert('Có lỗi xảy ra. Vui lòng thử lại.');
                        }
                    });
                },
                error: function(xhr) {
                    console.error('Có lỗi xảy ra khi kiểm tra đăng nhập:', xhr);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                }
            });
        });
    });
    $(document).ready(function() {
        $('.toggle-content').click(function() {
            var postDescription = $(this).siblings('.post-description'); // Truy cập đến div .post-description gần nhất
            var preview = postDescription.find('.content-preview');
            var fullContent = postDescription.find('.content-full');

            if (fullContent.is(':visible')) {
                // Ẩn nội dung đầy đủ và hiện nội dung tóm tắt
                fullContent.hide();
                preview.show();
                $(this).text('Xem thêm');
            } else {
                // Hiện nội dung đầy đủ và ẩn nội dung tóm tắt
                fullContent.show();
                preview.hide();
                $(this).text('Ẩn bớt');
            }
        });
    });

    $(document).ready(function() {
        $(document).on('click', '.save-post', function(e) {
            e.preventDefault();

            const postId = $(this).data('post-id');
            // console.log('Mở modal cho bài viết:', postId);

            $.ajax({
                url: '/check-login',
                method: 'GET',
                success: function(data) {
                    if (!data.isLoggedIn) {
                        alert('Bạn cần đăng nhập để lưu bài viết.');
                        window.location.href = '/login';
                        return;
                    }

                    // Đảm bảo xóa modal cũ trước khi mở modal mới
                    $('#folderModal').remove(); // Xóa modal cũ nếu tồn tại

                    // Load lại modal từ mã HTML
                    $('body').append(`
                    <div class="modal fade" id="folderModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Chọn Thư Mục</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Chọn hoặc tạo thư mục để lưu bài viết.</p>
                                    @if(!empty($folders) && count($folders) > 0)
                                        <select id="folderSelect" class="form-select">
                                            <option value="" disabled selected>Chọn thư mục</option>
                                            @foreach($folders as $folder)
                                                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <p>Chưa có thư mục nào. Vui lòng tạo thư mục mới.</p>
                                    @endif
                                    <input type="text" id="newFolderName" class="form-control" placeholder="Tên thư mục mới">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" class="btn btn-primary" id="saveToFolder">Lưu</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                    // Khởi tạo modal và hiển thị
                    const folderModal = new bootstrap.Modal(document.getElementById('folderModal'));
                    $('#folderModal').attr('data-post-id', postId);
                    folderModal.show();
                },
                error: function(xhr) {
                    console.error('Lỗi khi kiểm tra đăng nhập:', xhr);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                }
            });
        });

        // Xử lý lưu bài viết vào thư mục
        $(document).on('click', '#saveToFolder', function() {
            const postId = $('#folderModal').data('post-id');
            const folderId = $('#folderSelect').val();
            const newFolderName = $('#newFolderName').val().trim();

            if (!folderId && !newFolderName) {
                alert('Vui lòng chọn hoặc tạo thư mục!');
                return;
            }

            // Hiển thị loading spinner trong khi xử lý
            $('#saveToFolder').prop('disabled', true).text('Đang lưu...');

            // Nếu tạo thư mục mới
            if (newFolderName) {
                $.ajax({
                    url: '/folders',
                    method: 'POST',
                    data: {
                        name: newFolderName,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Lưu bài viết vào thư mục mới được tạo
                            savePost(postId, response.folder_id);
                        } else {
                            alert(response.message);
                            $('#saveToFolder').prop('disabled', false).text('Lưu');
                        }
                    },
                    error: function(jqXHR) {
                        console.error('Lỗi tạo thư mục:', jqXHR.responseText);
                        alert('Có lỗi xảy ra: ' + jqXHR.status + ' ' + jqXHR.statusText);
                        $('#saveToFolder').prop('disabled', false).text('Lưu');
                    }
                });
            } else {
                // Lưu bài viết vào thư mục đã chọn
                savePost(postId, folderId);
            }
        });

        // Hàm lưu bài viết vào thư mục
        function savePost(postId, folderId) {
            $.ajax({
                url: '/users/posts/save-post',
                method: 'POST',
                data: {
                    post_id: postId,
                    folder_id: folderId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('Bài viết đã được lưu!');
                        $('#folderModal').modal('hide');
                        $('.save-post[data-post-id="' + postId + '"]').replaceWith(
                            '<button class="btn btn-link unsave-post" data-post-id="' + postId + '"><i class="fas fa-bookmark"></i> Bỏ lưu</button>'
                        );
                    } else {
                        alert(response.message);
                    }
                    $('#saveToFolder').prop('disabled', false).text('Lưu');
                },
                error: function(jqXHR) {
                    console.error('Lỗi lưu bài viết:', jqXHR.responseText);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                    $('#saveToFolder').prop('disabled', false).text('Lưu');
                }
            });
        }
        // Hàm xử lý khi người dùng bấm "Bỏ lưu"
        $(document).on('click', '.unsave-post', function() {
            const postId = $(this).data('post-id');

            $.ajax({
                url: '/api/posts/' + postId + '/unsave',
                method: 'DELETE',
                data: {
                    post_id: postId,
                    _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
                },
                success: function(response) {
                    if (response.success) {
                        alert('Bài viết đã được bỏ lưu!');

                        // Thay đổi nút "Bỏ lưu" thành "Lưu"
                        $('.unsave-post[data-post-id="' + postId + '"]').replaceWith(
                            '<button class="btn btn-link save-post" data-post-id="' + postId + '"><i class="fas fa-bookmark"></i> Lưu</button>'
                        );
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(jqXHR) {
                    console.error(jqXHR.responseText);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                }
            });
        });
    });
    // Hiển thị modal đổi tên với folder id tương ứng
    $(document).on('click', '.rename-folder', function() {
        var folderId = $(this).data('folder-id');
        // Lấy form action hiện tại và thay thế {folder_id} bằng folderId thực
        var formAction = '{{ route("folders.rename", ["folder_id" => ":folder_id"]) }}';
        formAction = formAction.replace(':folder_id', folderId);

        // Cập nhật action của form
        $('#renameFolderForm').attr('action', formAction);

        // Mở modal
        $('#renameModal').modal('show');
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>