<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào Mừng Đến TeachTalks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Link CSS -->
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    <style>
        .like-button {
            color: black;
            text-decoration: none;
        }

        .like-button.liked {
            color: blue;
        }

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
            padding-top: 50px;
            padding-left: 10px;
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

        .post-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #888;
        }

        .post-meta {
            position: absolute;
            top: 10px;
            left: 10px;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: #888;
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

        .post-image {
            margin: 10px 0;
            text-align: center;
        }

        .post-image img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            /* Đảm bảo rằng container có chiều cao tối thiểu bằng chiều cao của viewport */
        }

        .main-content {
            display: flex;
            flex-direction: row;
            flex: 1;
        }

        .vertical-navbar {
            width: 200px;
            position: fixed;
            height: calc(100vh - 56px);
            /* Điều chỉnh để chiều cao của thanh điều hướng không vượt quá chiều cao của viewport */
            background: linear-gradient(223deg,
                    #00d2d3,
                    #3a7bd5,
                    #673BAE,
                    #8948b2,
                    #FF00FF);
            z-index: 1000; /*Đảm bảo phần tử luôn nằm trên các phần tử khác*/
            overflow-y: auto; /*Cho phép thanh cuộn dọc xuất hiện nếu nội dung bên trong vượt quá chiều cao phần tử*/
            flex-shrink: 0; /*Ngăn không cho phần tử bị co lại trong mô hình Flexbox, đảm bảo kích thước của nó không bị thay đổi*/
            z-index: 1000;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .welcome-content {
            margin-left: 200px;
            /* Tạo khoảng cách để tránh bị che khuất bởi thanh điều hướng dọc */
            padding: 20px;
            flex: 1;
            position: relative;
        }

        .row {
            margin-left: 200px;
            /* Khoảng cách bên trái để tránh bị chồng lên bởi vertical-navbar */
            margin-top: 20px;
            /* Khoảng cách từ welcome-content */
            /* Đặt z-index cao hơn để row nằm trên thanh điều hướng */
        }

        .navbar-toggler {
            margin-right: 10px;
            /* Đảm bảo khoảng cách hợp lý */
        }

        .navbar-collapse {
            background-color: #343a40;
            /* Màu nền đồng bộ với thanh điều hướng */
        }

    </style>
</head>

<body>
    <div class="container">
        <header class="p-3">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('storage/images/bookicon.png') }}" alt="Description">TechTalks
                </a>
                <div class="search-bar w-100">
                    <form class="input-group">
                        <input class="form-control me-2" type="search" placeholder="Tìm kiếm bài viết" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">
                            <i class="fas fa-search"></i>Search
                        </button>
                    </form>
                </div>
                @auth
                <li class="nav-item dropdown ms-3">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-circle">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#">{{ Auth::user()->name }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('users.profile', Auth::user()->id) }}">Thông tin cá nhân</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Đăng Xuất
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <ul class="nav-item ms-3">
                    <a class="nav-link" href="{{ route('login') }}">Đăng Nhập</a>
                </ul>
                @endauth
            </nav>

        </header>
        <!-- Menu điều hướng thu gọn cho màn hình lớn -->
        <div class="main-content">
            <div class="vertical-navbar d-none d-lg-block"> <!-- Ẩn đi trên màn hình nhỏ -->
                <nav class="navbar navbar-dark flex-column">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">Trang Chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('posts.index') }}">Bài Viết</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">Danh mục</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Menu điều hướng thu gọn cho màn hình nhỏ -->
            <nav class="navbar navbar-expand-lg navbar-dark d-lg-none" style="margin-bottom: 420px;"> <!-- Chỉ hiển thị trên màn hình nhỏ -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">Trang Chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('posts.index') }}">Bài Viết</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">Danh mục</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Bài viết mới</h5>
                        <p class="card-text">Các cuộc thảo luận mới nhất trong diễn đàn.</p>
                        <a href="{{ route('posts.index') }}" class="btn btn-primary">Xem Bài viết</a>
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
            <div class="post-container">
                <!-- Bài viết 1 -->
                <div class="post-card">
                    <div class="post-meta">
                        <img src="{{ asset('storage/images/bookicon.png') }}" alt="Avatar" class="post-avatar">
                        <span class="post-author">Đăng bởi: <strong>Nguyễn Văn A</strong></span> |
                        <span class="post-time">2 giờ trước</span>
                    </div>
                    <div class="vote-section">
                        <i class="fas fa-arrow-up"></i>
                        <span>10</span>
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="post-content">
                        <div class="post-title">Cách Quản Lý Thời Gian Hiệu Quả Cho Sinh Viên</div>
                        <div class="post-description">Sinh viên thường phải đối mặt với nhiều nhiệm vụ cùng lúc. Bài viết này sẽ hướng dẫn bạn cách quản lý thời gian hiệu quả.</div>
                        <!-- Hiển thị ảnh nếu người dùng đưa lên -->
                        <div class="post-image">
                            <img src="{{ asset('storage/images/abc.jpg') }}" alt="">
                        </div>
                        <div class="post-footer">
                            <div>
                                <span><i class="fas fa-comments"></i> 5 bình luận</span> |
                                <a href="#" class="like-button"><i class="fas fa-heart"></i> 50</a>
                                <button class="btn btn-link"><i class="fas fa-bookmark"></i> Lưu</button>
                                <button class="">
                                    Chia sẻ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Bài viết 2 -->
                <div class="post-card">
                    <div class="post-meta">
                        <img src="{{ asset('storage/images/avataricon.png') }}" alt="Avatar" class="post-avatar">
                        <span class="post-author">Đăng bởi: <strong>Nguyễn Văn B</strong></span> |
                        <span class="post-time">3 giờ trước</span>
                    </div>
                    <div class="vote-section">
                        <i class="fas fa-arrow-up"></i>
                        <span>20</span>
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="post-content">
                        <div class="post-title">Làm Thế Nào Để Tối Ưu Hóa Kỹ Năng Lập Trình</div>
                        <div class="post-description">Kỹ năng lập trình là một yếu tố quan trọng trong ngành công nghệ thông tin. Bài viết này sẽ chia sẻ cách tối ưu hóa kỹ năng lập trình của bạn.</div>
                        <!-- Hiển thị ảnh nếu người dùng đưa lên -->
                        <div class="post-image">
                            <img src="{{ asset('storage/images/abc.jpg') }}" alt="">
                        </div>
                        <div class="post-footer">
                            <div>
                                <span><i class="fas fa-comments"></i> 8 bình luận</span> |
                                <a href="#" class="like-button"><i class="fas fa-heart"></i> 100</a>
                                <button class="btn btn-link"><i class="fas fa-bookmark"></i> Lưu</button>
                                <button class="">
                                    Chia sẻ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </main>
        <footer class="mt-5 py-4">
            <div class="container text-center">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h5>Liên hệ với chúng tôi</h5>
                        <p>Email: <a href="mailto:ttp6889@gmail.com">ttp6889@gmail.com</a></p>
                        <p>Phone: 038-531-5971</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>TechTalks</h5>
                        <p>&copy; {{ date('Y') }} TechTalks. All rights reserved.</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Theo dõi chúng tôi</h5>
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin fa-2x"></i></a>
                    </div>
                </div>
                <hr class="my-4">
                <p class="text-muted small">Trang web này được phát triển bởi TechTalks.</p>
            </div>
        </footer>
    </div>
    <script>
        // JavaScript để thay đổi màu sắc khi bấm nút "Thích"
        document.querySelectorAll('.like-button').forEach(button => {
            button.addEventListener('click', function() {
                this.classList.toggle('liked');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>