<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Link CSS -->
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    <style>
        body {
            background: linear-gradient(135deg, #ffffff, #ffe6e6);
            font-family: 'Arial', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="welcome-section">
            <h3>Chào mừng đến với TechTalks</h3>
            <p>Đăng ký để có một trải nghiệm tốt nhất</p>
        </div>
        <div class="login-section">
            <h3>Đăng ký vào TechTalks</h3>

            <!-- Hiển thị lỗi nếu có -->
            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registrationForm">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="username" class="form-label">Tên người dùng</label>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required pattern=".{3,}" title="Tên người dùng phải có ít nhất 3 ký tự" autofocus>
                    @error('username')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Địa chỉ Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    @error('password')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
            </form>
            <!-- Đăng ký bằng Google -->
            <a href="{{ route('google.login') }}" class="btn btn-success">
                <i class="fab fa-google"></i> Đăng ký bằng Google
            </a>
            <div class="mt-3 text-center">
                <p>Bạn đã có tài khoản?</p><a href="{{ route('login') }}"> Đăng Nhập tại đây</a>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('registrationForm');
                if (form) {
                    form.addEventListener('submit', function(event) {
                        // event.preventDefault(); // Ngăn chặn gửi form
                        console.log('Form submitted');
                    });
                }
            });
        </script>
</body>

</html>