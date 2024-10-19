<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@latest"></script>
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    @yield('styles')
</head>

<body style="background-color:rgb(228 230 235);">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
                <div class="position-sticky">
                    <h4 class="text-center text-light py-3">Admin Dashboard</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active text-light" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        <hr class="bg-secondary">
                        <li class="nav-item">
                            <a class="nav-link text-light" href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users"></i> User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="{{ route('admin.posts.index') }}">
                                <i class="fas fa-file-alt"></i> Posts
                            </a>
                        </li>
                        <hr class="bg-secondary">
                        <li class="nav-item">
                            <a class="nav-link text-light" href="#">
                                <i class="fas fa-comments"></i> Comments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="{{ route('admin.categories.index') }}">
                                <i class="fas fa-book"></i> Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="{{ route('admin.reports.index') }}">
                                <i class="fas fa-file-alt"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="#">
                                <i class="fas fa-cogs"></i> Settings
                            </a>
                        </li>
                        <hr class="bg-secondary">
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                    <!-- Form đăng xuất -->
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </nav>

            <!-- Main Content -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    @php
    $activityLabelsJson = json_encode(array_column($activityData, 'date'));
    $activityPostsCountsJson = json_encode(array_column($activityData, 'posts_count'));
    $activityCommentsCountsJson = json_encode(array_column($activityData, 'comments_count'));
    $statusLabelsJson = json_encode($statusLabels);
    $statusCountsJson = json_encode($statusCounts);
    @endphp

    <script>
        $(document).ready(function() {
            // Biến dữ liệu biểu đồ trạng thái bài viết
            var statusLabels = JSON.parse('{!! json_encode($statusLabels) !!}');
            var statusCounts = JSON.parse('{!! json_encode($statusCounts) !!}');

            // Dữ liệu cho biểu đồ hoạt động
            var activityLabels = JSON.parse('{!! $activityLabelsJson !!}'); //chuyển đổi chuỗi (string) ở định dạng JSON thành một đối tượng JavaScript (JavaScript Object)
            var activityPostsCounts = JSON.parse('{!! $activityPostsCountsJson !!}');
            var activityCommentsCounts = JSON.parse('{!! $activityCommentsCountsJson !!}');

            // Lấy context của canvas cho biểu đồ
            var ctx = $('#postStatusChart');

            // Kiểm tra nếu phần tử canvas tồn tại trước khi tạo biểu đồ
            if (ctx.length) {
                var postStatusChart = new Chart(ctx, {
                    type: 'doughnut', // Loại biểu đồ
                    data: {
                        labels: statusLabels, // Sử dụng biến JavaScript đã được truyền
                        datasets: [{
                            label: 'Tỷ lệ bài viết',
                            data: statusCounts, // Sử dụng biến JavaScript đã được truyền
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.6)', // Màu xanh ngọc (cyan) với độ trong suốt là 60%
                                'rgba(255, 159, 64, 0.6)', // Màu cam (orange) với độ trong suốt là 60%
                                'rgba(153, 102, 255, 0.6)', // Màu tím (purple) với độ trong suốt là 60%
                                'rgba(255, 99, 132, 0.6)' // Màu hồng (pink) với độ trong suốt là 60%
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom', // Vị trí của chú thích biểu đồ
                            },
                            title: {
                                display: true,
                                text: 'Tỷ lệ bài viết theo trạng thái'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        var total = statusCounts.reduce((a, b) => a + b, 0);
                                        var currentValue = tooltipItem.raw;
                                        var percentage = Math.floor((currentValue / total) * 100); // Tính phần trăm
                                        return `${tooltipItem.label}: ${currentValue} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            // Biểu đồ hoạt động người dùng
            var ctxActivity = $('#recentActivityChart');

            if (ctxActivity.length) {
                var recentActivityChart = new Chart(ctxActivity, {
                    type: 'line',
                    data: {
                        labels: activityLabels, // Các nhãn theo ngày
                        datasets: [{
                            label: 'Bài viết',
                            data: activityPostsCounts, // Dữ liệu bài viết
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true,
                            pointRadius: 0, // Không hiển thị các điểm để tránh nhồi nhét
                            borderWidth: 1
                        }, {
                            label: 'Bình luận',
                            data: activityCommentsCounts, // Dữ liệu bình luận
                            borderColor: 'rgba(255, 159, 64, 1)',
                            backgroundColor: 'rgba(255, 159, 64, 0.2)',
                            fill: true,
                            pointRadius: 0, // Không hiển thị các điểm để tránh nhồi nhét
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            },
                            title: {
                                display: true,
                                text: 'Hoạt động của người dùng'
                            },
                            zoom: {
                                pan: {
                                    enabled: true, // Kích hoạt kéo để cuộn
                                    mode: 'x',
                                },
                                zoom: {
                                    wheel: {
                                        enabled: true, // Kích hoạt zoom bằng chuột
                                    },
                                    mode: 'x', // Chỉ zoom theo trục X
                                }
                            }
                        },
                        scales: {
                            x: {
                                type: 'time', // Hiển thị trục thời gian
                                time: {
                                    unit: 'day', // Đơn vị ngày
                                    tooltipFormat: 'PPP',
                                    displayFormats: {
                                        day: 'MMM d' // Định dạng hiển thị ngày
                                    }
                                },
                                ticks: {
                                    autoSkip: true, // Tự động bỏ bớt các nhãn không cần thiết
                                    maxTicksLimit: 10 // Giới hạn tối đa số lượng nhãn
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        },
                        // Giảm số lượng điểm cần hiển thị (Decimation)
                        decimation: {
                            enabled: true,
                            algorithm: 'lttb', // Lấy mẫu theo thuật toán lttb (đường thấp hơn mức thấp nhất)
                            samples: 100 // Chỉ hiển thị 100 điểm dữ liệu
                        }
                    }
                });
            }

            function updateDateTime() {
                var today = new Date();

                // Tạo các chuỗi ngày, giờ, phút, giây
                var dayNames = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
                var day = dayNames[today.getDay()];
                var date = today.getDate();
                var month = today.getMonth() + 1; // Tháng trong JavaScript tính từ 0
                var year = today.getFullYear();
                var hour = today.getHours();
                var minute = today.getMinutes();
                var second = today.getSeconds();

                // Thêm số 0 vào phút và giây nếu nhỏ hơn 10
                if (minute < 10) minute = '0' + minute;
                if (second < 10) second = '0' + second;

                // Định dạng chuỗi ngày giờ
                var formattedDateTime = `${day}, ${date}/${month}/${year} - ${hour}:${minute}:${second}`;

                // Hiển thị trong phần tử có id="currentDateTime"
                $('#currentDateTime').text(formattedDateTime);
            }

            // Cập nhật ngày giờ ngay khi trang được tải
            updateDateTime();

            // Cập nhật mỗi giây
            setInterval(updateDateTime, 1000);
        });
    </script>

    @yield('scripts')
</body>

</html>