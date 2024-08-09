<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Laravel Forum</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Link to your CSS file -->
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>

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
                                <a class="nav-link" href="{{ url('/') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('posts.index') }}">Posts</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('categories.index') }}">Categories</a>
                            </li>
                            @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-circle">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="#">{{ Auth::user()->name }}</a></li>
                                    <!-- <li><a class="dropdown-item" href="{{ route('users.edit', Auth::user()->id) }}">Edit Profile</a></li> lỗi chưa có user.edit -->
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
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
                    <input class="form-control me-2" type="search" placeholder="Search posts" aria-label="Search">
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
        </main>
        <footer class="mt-4 text-center">
            <p>&copy; {{ date('Y') }} Your Application Name. All rights reserved.</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>