<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Người Dùng</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Link CSS -->
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>
<style>
    body {
        background: linear-gradient(135deg, #ffffff, #ffe6e6);
    }

    /* Container chính bao gồm header, content và footer */
    body>div.container {
        min-height: 100vh;
        /* Đảm bảo container chiếm ít nhất 100% chiều cao viewport */
        display: flex;
        flex-direction: column;
    }

    /* Nội dung chính của trang */
    .container .content {
        flex: 1;
        /* Chiếm toàn bộ không gian còn lại */
    }

    h1 {
        text-align: center;
    }

    .col-md-4 {
        text-align: center;
    }
</style>

<body>
    <div class="container" style="background-color: #ffffff;">
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
                                <a class="nav-link" href="{{ route('users.index') }}">Bài Viết</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('categories.index') }}">Danh Mục</a>
                            </li>
                            @auth
                            <li class="nav-item dropdown">
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
                                    <li><a class="dropdown-item" href="#">Thông tin cá nhân</a></li>
                                    <li><a class="dropdown-item" href="{{ route('users.posts.published') }}">Bài Viết Đã Xuất Bản</a></li>
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
        <div class="container mt-5">
            <h1>Thông Tin Người Dùng</h1>

            <!-- Nội dung của bạn ở đây -->
            <div class="content">
                <!-- Hiển thị thông báo thành công nếu có -->
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Hiển thị thông tin người dùng -->
                <div class="row">
                    <div class="col-md-4">
                        @if(isset($user))
                        @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Avatar" class="img-thumbnail">
                        @else
                        <img src="{{ asset('storage/images/avataricon.png') }}" alt="Avatar Mặc Định" class="img-thumbnail">
                        @endif
                        @else
                        <p>Biến user không tồn tại.</p>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên:</label>
                            <p>{{ $user->username }}</p>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <p>{{ $user->email }}</p>
                        </div>

                        <!-- Hiển thị danh sách các bài viết của người dùng -->
                        <div class="mb-3">
                            <h5 class="mb-3">Bài Viết Của Tôi</h5>
                            <p class="text-muted">Số lượng bài viết đã xuất bản: <strong>{{ $publishedCount }}</strong></p>
                            @if(Auth::id() === $user->id)
                            <p class="text-muted">Số lượng bài viết ở dạng draft: <strong>{{ $draftCount }}</strong></p>
                            <a href="{{ route('users.posts.drafts') }}" class="btn btn-success">Những bài viết dạng draft</a>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="create_at" class="form-label">Ngày tham gia:</label>
                            <p>{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</p>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái tài khoản:</label>
                            <p>{{ ucfirst($user->status) }}</p>
                        </div>

                        <div class="mb-3">
                            <label for="post_count" class="form-label">Số lượng bài viết:</label>
                            <p>{{ $user->post_count }}</p>
                        </div>

                        <div class="mb-3">
                            <label for="favorite_posts" class="form-label">Bài viết yêu thích:</label>
                            <p>{{ $user->favorite_posts ?? 'Chưa có bài viết yêu thích' }}</p>
                        </div>

                        <!-- Kiểm tra nếu người dùng hiện tại là chủ sở hữu -->
                        @if(Auth::check() && Auth::user()->id === $user->id)
                        <!-- Nút chỉnh sửa thông tin -->
                        <a href="{{ route('users.profile.edit', $user->id) }}" class="btn btn-primary">Chỉnh Sửa Thông Tin</a>

                        <!-- Nút để quay lại trang trước -->
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Quay lại</a>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>