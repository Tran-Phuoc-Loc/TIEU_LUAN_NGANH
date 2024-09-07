<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
</head>
<style>
    body {
        font-family: Arial, sans-serif;
    }

    #sidebar {
        height: 100vh;
        padding-top: 1rem;
    }

    .nav-link {
        padding: 0.75rem 1.25rem;
        font-size: 1rem;
    }

    .card {
        margin-bottom: 1rem;
    }

    .card-title {
        font-size: 1.25rem;
    }

    .card-text {
        font-size: 1.5rem;
    }

    .list-group-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky">
                    <h4 class="text-center">Admin Dashboard</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-file-alt"></i> Posts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-comments"></i> Comments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-book"></i> Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cogs"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h2>Dashboard Overview</h2>
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Tổng số người dùng</h5>
                                <p class="card-text">150</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Bài viết</h5>
                                <p class="card-text">200</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <h5 class="card-title">Lượt yêu thích</h5>
                                <p class="card-text">15</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Số người đăng ký hôm nay</h5>
                                <p class="card-text">8</p>
                            </div>
                        </div>
                    </div>
                </div>

                <h3>Hoạt động gần đây</h3>
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Student123</strong> Tạo bài viết mới : <em>"Mẹo cho kỳ thi"</em>
                        <span class="badge badge-primary float-right">2 hours ago</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Student456</strong> Đã cập nhật ảnh đại diện của họ.
                        <span class="badge badge-primary float-right">5 hours ago</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Student789</strong> đã bình luận về một bài đăng.
                        <span class="badge badge-primary float-right">1 day ago</span>
                    </li>
                </ul>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>