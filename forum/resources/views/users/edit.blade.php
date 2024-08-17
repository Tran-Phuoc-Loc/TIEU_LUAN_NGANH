<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa hồ sơ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Link CSS -->
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>

<body>
    <div class="container mt-5">
        <h1>Chỉnh sửa hồ sơ</h1>
        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Tên -->
            <div class="mb-3">
                <label for="name" class="form-label">Tên</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->username) }}" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>

            <!-- Ảnh đại diện -->
            <div class="mb-3">
                <label for="avatar" class="form-label">Ảnh đại diện</label>
                <input type="file" class="form-control" id="avatar" name="avatar">
                @if($user->avatar)
                <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="Ảnh đại diện hiện tại" class="mt-2">
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật hồ sơ</button>
        </form>
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