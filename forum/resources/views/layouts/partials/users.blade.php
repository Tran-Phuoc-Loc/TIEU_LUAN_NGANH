<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Chào Mừng Đến TeachTalks')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
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
            width: 100%;
            height: 400px;
            overflow: hidden;
            /*đảm bảo kích thước không quá phần tử chứa*/
        }

        .post-image img {
            max-width: 100%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /*căn chỉnh phù hợp khung chứa, giữ nguyên khung hình*/
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container {
            display: flex;
            flex-direction: column;
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
            z-index: 1000;
            /*Đảm bảo phần tử luôn nằm trên các phần tử khác*/
            overflow-y: auto;
            /*Cho phép thanh cuộn dọc xuất hiện nếu nội dung bên trong vượt quá chiều cao phần tử*/
            flex-shrink: 0;
            /*Ngăn không cho phần tử bị co lại trong mô hình Flexbox, đảm bảo kích thước của nó không bị thay đổi*/
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
                            <a href="{{ route('posts.create') }}" class="btn btn-Success">Tạo Bài viết</a>
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
                            <a class="nav-link" href="{{ route('users.index') }}">Bài Viết</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">Danh mục</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main>
                @yield('content')
            </main>
        </div>

        @include('layouts.partials.footer')
    </div>
    <script>
        document.querySelectorAll('.like-button').forEach(button => {
            button.addEventListener('click', function() {
                this.classList.toggle('liked');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>