<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Nơi chia sẻ và thảo luận về công nghệ.">
    <meta name="keywords" content="TechTalks, công nghệ, thảo luận">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chào Mừng Đến TeachTalks')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    @yield('css') <!-- Hiển thị CSS riêng cho mỗi trang -->
    <style>
        /* Container chính */
        .container {
            display: flex;
            flex-direction: column;
        }

        /* Kiểu cho các bài viết */
        .post-container {
            margin: 0 auto;
            padding: 0;
            max-width: 100%;
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
            height: 400px;
            overflow: hidden;
            /* Đảm bảo kích thước không vượt quá phần tử chứa */
        }

        .post-image img {
            max-width: 100%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Căn chỉnh phù hợp khung chứa, giữ nguyên khung hình */
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Nội dung chính */
        .main-content {
            display: flex;
            flex-direction: row;
            flex: 1;
        }

        /* Nội dung chào mừng */
        .welcome-contents {
            margin-left: 200px;
            padding: 20px;
            flex: 1;
            position: relative;
            max-width: 1200px;
            /* Đặt độ rộng tối đa cho container */
        }

        /* Thanh điều hướng dọc */
        .vertical-navbar {
            width: 200px;
            position: fixed;
            height: calc(100vh - 56px);
            /* Điều chỉnh để chiều cao của thanh điều hướng không vượt quá chiều cao của viewport */
            background: linear-gradient(223deg, #00d2d3, #3a7bd5, #673BAE, #8948b2, #FF00FF);
            z-index: 1000;
            /* Đảm bảo phần tử luôn nằm trên các phần tử khác */
            overflow-y: auto;
            /* Cho phép thanh cuộn dọc xuất hiện nếu nội dung bên trong vượt quá chiều cao phần tử */
            flex-shrink: 0;
            /* Ngăn không cho phần tử bị co lại trong mô hình Flexbox, đảm bảo kích thước của nó không bị thay đổi */
        }

        /* Nội dung chào mừng */
        .welcome-content {
            margin-left: 200px;
            /* Tạo khoảng cách để tránh bị che khuất bởi thanh điều hướng dọc */
            padding: 20px;
            flex: 1;
            position: relative;
        }

        /* Hàng */
        .row {
            margin: auto;
            margin-left: 215px;
            /* Khoảng cách bên trái để tránh bị chồng lên bởi vertical-navbar */
            margin-top: 20px;
            /* Khoảng cách từ welcome-content */
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

        .dropdown-item {
            color: #000;
            /* Màu chữ của các item */
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
    </style>
</head>

<body>
    <div class="container">
        <header class="p-3">
            <!-- Navbar chính -->
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('storage/images/bookicon.png') }}" alt="Description">TechTalks
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @auth
                        <li class="nav-item dropdown ms-3">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-circle">
                                    @if(Auth::user()->profile_picture)
                                    @php($imagePath = asset('storage/' . Auth::user()->profile_picture))
                                    <img src="{{ $imagePath }}" alt="Ảnh đại diện" class="img-fluid" style="border-radius: 50%;">
                                    @else
                                    <img src="{{ asset('storage/images/avataricon.png') }}" alt="Ảnh đại diện mặc định" class="img-fluid" style="border-radius: 50%;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    @endif
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#">{{ Auth::user()->name }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('users.profile.index', Auth::user()->id) }}">Thông tin cá nhân</a></li>
                                <li><a class="dropdown-item" href="{{ route('users.posts.published') }}">Bài Viết Đã Xuất Bản</a></li>
                                <li><a class="dropdown-item" href="{{ route('users.posts.savePost') }}">Bài Viết Đã Lưu</a></li>
                                <li>
                                    <a href="{{ route('notifications.index') }}"
                                        class="dropdown-item {{ auth()->user()->unreadNotifications->count() > 0 ? 'new-notification' : '' }}">
                                        Thông báo
                                        @if(auth()->user()->unreadNotifications->count() > 0)
                                        <span class="badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('users.groups.index') }}">Danh sách các nhóm tham gia</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng Xuất</a>
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

            <!-- Thanh tìm kiếm -->
            <div class="search-bar mt-3">
                <form class="input-group" action="{{ url('users/posts') }}" method="GET">
                    <input class="form-control" type="search" name="query" placeholder="Tìm kiếm bài viết" aria-label="Search" value="{{ request('query') }}">
                    <button class="btn btn-outline-success" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
            </div>
        </header>

        <div class="main-content">
            <!-- Menu điều hướng cho màn hình lớn -->
            <div class="vertical-navbar d-none d-lg-block">
                <nav class="navbar navbar-dark flex-column">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">Trang Chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">Bài Viết</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">Danh mục</a>
                        </li>
                    </ul>
                </nav>
                <hr class="my-4">
                <nav class="navbar navbar-dark flex-column">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{ route('users.posts.create') }}" class="btn btn-success">Tạo Bài viết</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('users.groups.create') }}" class="btn btn-success">Tạo Group</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('groups.chat', $group->id) }}">Chat trong nhóm</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Menu điều hướng cho màn hình nhỏ -->
            <nav class="navbar navbar-dark d-lg-none fixed-bottom">
                <div class="container-fluid">
                    <div class="d-flex justify-content-around w-100">
                        <a class="nav-link" href="{{ url('/') }}">Trang Chủ</a>
                        <a class="nav-link" href="{{ route('users.index') }}">Bài Viết</a>
                        <a href="{{ route('users.posts.create') }}" class="btn btn-light">
                            <span class="circle-icon"><i class="fas fa-plus"></i></span>
                        </a>
                        <a class="nav-link" href="{{ route('categories.index') }}">Danh mục</a>
                        <a class="nav-link" href="#">Liên hệ</a>
                    </div>
                </div>
            </nav>
        </div>

        <main class="main">
            <!-- Nội dung chính -->
            @yield('content')
        </main>


    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        const commentModal = $('#commentModal'); // Modal để hiển thị bình luận
        const commentForm = $('#commentForm'); // Form để gửi bình luận
        const closeButton = $('.close');
        const commentsList = $('.comments-list'); // Danh sách bình luận trong modal
        const loginModal = $('#loginModal');

        // Hiển thị modal bình luận khi người dùng nhấn vào nút
        $('.comment-toggle').on('click', function() {
            const postId = $(this).data('post-id'); // Lấy ID bài viết từ thuộc tính
            const postTitle = $(this).closest('.post-card').find('.post-title').text(); // Lấy tiêu đề bài viết

            $('#modalPostTitle').text(postTitle); // Cập nhật tiêu đề trong modal
            commentModal.show(); // Hiển thị modal

            // Gọi API để lấy danh sách bình luận
            $.get(`/posts/${postId}/comments`, function(data) {
                commentsList.empty(); // Làm sạch danh sách bình luận
                if (data.comments.length > 0) {
                    data.comments.forEach(comment => {
                        const createdAt = moment(comment.created_at).fromNow(); // Xử lý ngày tháng
                        const likesCount = (typeof comment.likes_count !== 'undefined') ? comment.likes_count : 0; // Đặt mặc định là 0 nếu undefined
                        const commentHtml = `
                    <div class="comment">
                        <img src="${comment.user.avatar_url ? '/storage/' + comment.user.avatar_url : '/storage/images/avataricon.png'}" alt="Avatar" class="comment-avatar">
                        <strong>${comment.user.username}</strong>:<small>${createdAt}</small>
                        <h6>${comment.content}</h6>
                        ${comment.image_url ? `<div class="comment-image"><img src="/storage/${comment.image_url}" alt="Comment Image"></div>` : ''}
                        <div class="comment-actions">
                            <button class="like-button" data-comment-id="${comment.id}">
                                <i class="far fa-thumbs-up"></i>  <span class="like-count">${comment.likes_count}</span>
                            </button>
                            <button class="share-button" data-comment-id="${comment.id}">
                                <i class="fas fa-share-alt"></i> Chia sẻ
                            </button>
                            <button class="relay-button" data-comment-id="${comment.id}">
                                <i class="fas fa-retweet"></i> Relay
                            </button>
                        </div>
                    </div>
                    `;
                        commentsList.append(commentHtml);
                    });
                } else {
                    commentsList.append('<p>Chưa có bình luận nào.</p>');
                }
                commentForm.attr('action', `/posts/${postId}/comments`); // Cập nhật thuộc tính action của form
            });
        });

        // Đóng modal khi nhấn nút đóng
        closeButton.on('click', function() {
            commentModal.hide();
        });

        // Đóng modal khi nhấn ra ngoài modal
        $(window).on('click', function(event) {
            if ($(event.target).is(commentModal)) {
                commentModal.hide();
            }
        });

        // Gửi bình luận
        commentForm.on('submit', function(e) {
            e.preventDefault(); // Ngăn chặn tải lại trang
            const formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success && data.comment) {
                        const likesCount = data.comment.likes_count || 0; // Đặt mặc định là 0 nếu undefined
                        const commentHtml = `
                        <div class="comment">
                            <img src="${data.comment.user.avatar_url ? '/storage/' + data.comment.user.avatar_url : '/storage/images/avataricon.png'}" alt="Avatar" class="comment-avatar">
                            <strong>${data.comment.user.username}</strong>:<small>Vừa xong</small>
                            <h6>${data.comment.content}</h6>
                            ${data.comment.image_url ? `<div class="comment-image"><img src="/storage/${data.comment.image_url}" alt="Comment Image"></div>` : ''}
                            <div class="comment-actions">
                                <button class="like-button" data-comment-id="${data.comment.id}">
                                    <i class="far fa-thumbs-up"></i>  <span class="like-count">${data.comment.likes_count}</span>
                                </button>
                                <button class="share-button" data-comment-id="${data.comment.id}">
                                    <i class="fas fa-share-alt"></i> Chia sẻ
                                </button>
                                <button class="relay-button" data-comment-id="${data.comment.id}">
                                    <i class="fas fa-retweet"></i> Relay
                                </button>
                            </div>
                        </div>
                    `;
                        commentsList.append(commentHtml);
                        commentForm[0].reset(); // Đặt lại form
                    } else {
                        alert('Đã có lỗi xảy ra. Vui lòng thử lại.');
                    }
                },
                error: function(xhr) {
                    console.error('Có lỗi xảy ra:', xhr);
                }
            });
        });

        // Xử lý sự kiện nhấn nút like
        $(document).on('click', '.like-button', function(e) {
            e.preventDefault(); // Ngăn chặn hành động mặc định của link

            const button = $(this);
            const commentId = button.data('comment-id'); // Lấy ID bình luận
            const postId = button.data('post-id'); // Lấy ID bài viết
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
                        likeUrl = `users/posts/${postId}/like`; // URL cho like bài viết
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

        // Đóng modal đăng nhập
        $('.close').on('click', function() {
            loginModal.hide();
        });

        $(window).on('click', function(event) {
            if ($(event.target).is(loginModal)) {
                loginModal.hide();
            }
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
                var preview = $(this).siblings('.post-description').find('.content-preview');
                var fullContent = $(this).siblings('.post-description').find('.content-full');

                if (fullContent.is(':visible')) {
                    fullContent.hide();
                    preview.show();
                    $(this).text('Xem thêm');
                } else {
                    fullContent.show();
                    preview.hide();
                    $(this).text('Ẩn bớt');
                }
            });
        });
    });
    $(document).ready(function() {
        // Khi nhấn nút Lưu bài viết
        $('.save-post').on('click', function() {
            const postId = $(this).data('post-id');
            $('#folderModal').data('post-id', postId).modal('show');
        });

        // Xử lý lưu bài viết vào thư mục
        $('#saveToFolder').on('click', function() {
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
                            savePost(postId, response.folder_id);
                        } else {
                            alert(response.message);
                            $('#saveToFolder').prop('disabled', false).text('Lưu');
                        }
                    },
                    error: function(jqXHR) {
                        console.error(jqXHR.responseText);
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
                        $('.save-post[data-post-id="' + postId + '"]').replaceWith('<button class="btn btn-link" disabled><i class="fas fa-bookmark"></i> Đã lưu</button>');
                    } else {
                        alert(response.message);
                    }
                    $('#saveToFolder').prop('disabled', false).text('Lưu');
                },
                error: function(jqXHR) {
                    console.error(jqXHR.responseText);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                    $('#saveToFolder').prop('disabled', false).text('Lưu');
                }
            });
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>