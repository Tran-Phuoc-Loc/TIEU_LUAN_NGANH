<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Link CSS -->
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>

<body>
    <div class="container text-center">
        <h1 class="display-1">403</h1>
        <h2 class="display-4">Quyền Truy Cập Bị Từ Chối</h2>
        <p class="lead">Chúng tôi rất tiếc, bạn không có quyền truy cập vào trang này.</p>
        <p>Vui lòng kiểm tra quyền truy cập của bạn hoặc quay lại trang chính để tiếp tục khám phá.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Quay lại Trang Chủ</a>
    </div>
</body>

</html>