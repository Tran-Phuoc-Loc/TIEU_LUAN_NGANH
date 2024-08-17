<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào Mừng Đến TeachTalks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Link CSS  -->
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>
<style>
    /* Màu ban đầu của nút "Thích" là trắng */
    .like-button {
        color: black;
        text-decoration: none; /* Loại bỏ gạch chân */
    }

    /* Khi nút được bấm, màu sẽ thay đổi sang màu xanh */
    .like-button.liked {
        color: blue;
        /* Bạn có thể thay màu này theo ý thích */
    }
</style>

<body>
    <div class="container">
        <header class="p-3">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('storage/images/bookicon.png') }}" alt="Description">TechTalks</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                        <ul class="navbar-nav">
                            <li>
                                <a class="nav-link" href="{{ url('/') }}">Trang Chủ</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('posts.index') }}">Bài Viết</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('categories.index') }}">Danh Mục</a>
                            </li>
                            @auth
                            <li class="nav-item dropdown">
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
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Đăng Nhập</a>
                            </li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <main class="mt-4">
            <div class="welcome-content">
                <h1>Chào mừng bạn đến với <strong>TechTalks</strong> <br> Hãy tham gia cùng chúng tôi và bắt đầu thảo luận ngay hôm nay!</h1>
                <p>Trang chào mừng này là nơi bắt đầu cho hành trình của bạn trong cộng đồng <strong>TechTalks</strong>.</p>
                <p>Đừng bỏ lỡ cơ hội để tham gia cùng chúng tôi trong những cuộc thảo luận sôi động về công nghệ. Khám phá, chia sẻ và học hỏi ngay hôm nay!.</p>
                <p></a>.</p>
            </div>
            <div class="search-bar">
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Tìm kiếm bài viết" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i>Search</button>
                </form>
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
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Cách Quản Lý Thời Gian Hiệu Quả Cho Sinh Viên</h5>
                            <p class="card-text">Sinh viên thường phải đối mặt với nhiều nhiệm vụ cùng lúc. Bài viết này sẽ hướng dẫn bạn cách quản lý thời gian hiệu quả.</p>
                            <a href="#" class="btn btn-primary">Đọc thêm</a>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-link like-button"><i class="fas fa-thumbs-up"></i> Thích</button>
                                <button class="btn btn-link"><i class="fas fa-bookmark"></i> Lưu</button>
                            </div>
                            <span><i class="fas fa-comments"></i> 10 Bình luận</span>
                        </div>
                    </div>
                </div>

                <!-- Bài viết 2 -->
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Bí Quyết Học Tập Tốt Nhất Cho Các Kỳ Thi</h5>
                            <p class="card-text">Kỳ thi luôn là thời điểm căng thẳng. Bài viết này cung cấp những bí quyết giúp bạn nắm bắt kiến thức tốt hơn và làm bài thi hiệu quả.</p>
                            <a href="#" class="btn btn-primary">Đọc thêm</a>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-link"><i class="fas fa-thumbs-up"></i> Thích</button>
                                <button class="btn btn-link"><i class="fas fa-bookmark"></i> Lưu</button>
                            </div>
                            <span><i class="fas fa-comments"></i> 8 Bình luận</span>
                        </div>
                    </div>
                </div>

                <!-- Bài viết số 3 -->
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Hướng Dẫn Săn Học Bổng Cho Sinh Viên</h5>
                            <p class="card-text">Săn học bổng là một trong những cách tốt nhất để giảm gánh nặng tài chính khi học đại học. Bài viết này sẽ hướng dẫn bạn cách tìm kiếm và nộp đơn xin học bổng hiệu quả.</p>
                            <a href="#" class="btn btn-primary">Đọc thêm</a>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-link"><i class="fas fa-thumbs-up"></i> Thích</button>
                                <button class="btn btn-link"><i class="fas fa-bookmark"></i> Lưu</button>
                            </div>
                            <span><i class="fas fa-comments"></i> 12 Bình luận</span>
                        </div>
                    </div>
                </div>
                
                <!-- Bài viết 4 -->
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Những Ngành Nghề Hot Cho Sinh Viên CNTT Trong Tương Lai</h5>
                            <p class="card-text">Ngành CNTT đang phát triển mạnh mẽ. Bài viết này sẽ điểm qua những ngành nghề hứa hẹn cho sinh viên CNTT.</p>
                            <a href="#" class="btn btn-primary">Đọc thêm</a>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-link"><i class="fas fa-thumbs-up"></i> Thích</button>
                                <button class="btn btn-link"><i class="fas fa-bookmark"></i> Lưu</button>
                            </div>
                            <span><i class="fas fa-comments"></i> 25 Bình luận</span>
                        </div>
                    </div>
                </div>

                <!-- Bài viết 5 -->
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Lợi Ích Của Việc Tham Gia Câu Lạc Bộ Sinh Viên</h5>
                            <p class="card-text">Tham gia các câu lạc bộ sinh viên không chỉ giúp bạn phát triển kỹ năng mà còn tạo dựng mối quan hệ và trải nghiệm thú vị.</p>
                            <a href="#" class="btn btn-primary">Đọc thêm</a>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-link"><i class="fas fa-thumbs-up"></i> Thích</button>
                                <button class="btn btn-link"><i class="fas fa-bookmark"></i> Lưu</button>
                            </div>
                            <span><i class="fas fa-comments"></i> 5 Bình luận</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="mt-5 py-4 ">
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