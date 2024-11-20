<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa hồ sơ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>

<body>
    <div class="container" style="background-color: #ffffff;">
        <header class="p-3">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('storage/images/bookicon.png') }}" alt="Description" loading="lazy">TechTalks</a>
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
                                    <li><a class="dropdown-item" href="{{ route('users.posts.published') }}">Bài Viết Đã Xuất Bản</a></li>
                                    <li><a class="dropdown-item" href="{{ route('users.posts.savePost') }}">Bài Viết Đã Lưu</a></li>
                                    <li>
                                        <a href="{{ route('notifications.index') }}"
                                            class="dropdown-item {{ auth()->user()->unreadNotifications->count() > 0 ? 'new-notification' : '' }}">
                                            <i class="fas fa-bell"></i> Thông báo
                                            @if(auth()->user()->unreadNotifications->count() > 0)
                                            <span class="badge bg-danger rounded-pill">{{ auth()->user()->unreadNotifications->count() }}</span>
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
            <h1>Chỉnh sửa hồ sơ</h1>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('users.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Tên -->
                <div class="mb-3">
                    <label for="username" class="form-label">Tên</label>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" disabled>
                </div>

                <!-- Ảnh đại diện -->
                <div class="mb-3">
                    <label for="avatar" class="form-label">Ảnh đại diện</label>
                    <input type="file" class="form-control" id="avatar" name="avatar">
                    @if($user->profile_picture)
                    <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Ảnh đại diện hiện tại" class="mt-2" style="max-width: 150px;" loading="lazy">
                    @endif
                </div>

                <!-- Ảnh nền -->
                <div class="mb-3">
                    <label for="cover_image" class="form-label">Ảnh nền</label>
                    <input type="file" class="form-control" id="cover_image" name="cover_image">
                    @if($user->cover_image)
                    <img src="{{ asset('storage/' . $user->cover_image) }}" alt="Ảnh nền hiện tại" class="mt-2" style="max-width: 100%; height: auto;" loading="lazy">
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">Cập nhật hồ sơ</button>
            </form>
        </div>

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