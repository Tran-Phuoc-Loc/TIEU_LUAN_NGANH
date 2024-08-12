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

<body>
    <div class="container mt-5">
        <h1>Thông Tin Người Dùng</h1>

        <!-- Hiển thị thông báo thành công nếu có -->
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <!-- Hiển thị thông tin người dùng -->
        <div class="row">
            <div class="col-md-4">
                @if($user->avatar)
                <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="Avatar" class="img-thumbnail" style="max-width: 150px;">
                @else
                <img src="{{ asset('storage/images/avataricon.png') }}" alt="Avatar" class="img-thumbnail" style="max-width: 150px;">
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
                    <h5>Bài Viết Của Tôi</h5>
                    @if ($posts->isEmpty())
                    <p>Chưa có bài viết nào.</p>
                    @else
                    <ul class="list-group">
                        @foreach ($posts as $post)
                        <li class="list-group-item">
                            <a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a>
                            <p>{{ Str::limit($post->content, 100) }}</p>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>

                <!-- Nút chỉnh sửa thông tin -->
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">Chỉnh Sửa Thông Tin</a>

                <!-- Nút để quay lại trang trước -->
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>