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
                        <li class="nav-item">
                            <a class="nav-link text-light" href="{{ route('admin.groups.index') }}">
                                <i class="fas fa-user-group"></i> Group
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

    @if(isset($statusLabels) && isset($statusCounts))
        @php
            $statusLabelsJson = json_encode($statusLabels);
            $statusCountsJson = json_encode($statusCounts);
        @endphp
    @else
        @php
            $statusLabelsJson = json_encode([]); // Mảng rỗng
            $statusCountsJson = json_encode([]); // Mảng rỗng
        @endphp
    @endif

    <script>
        $(document).ready(function() {
            // Biến dữ liệu biểu đồ trạng thái bài viết
            var statusLabels = JSON.parse('{!! $statusLabelsJson !!}');
            var statusCounts = JSON.parse('{!! $statusCountsJson !!}');

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
            // Khi người dùng thay đổi bộ lọc thời gian
            $('#timeRangeFilter').change(function() {
                var selectedRange = $(this).val(); // Lấy giá trị của khoảng thời gian
                loadActivityData(selectedRange); // Gọi hàm để tải dữ liệu mới
            });

            function loadActivityData(range) {
                $.ajax({
                    url: '/get-activity-data', // API hoặc route để lấy dữ liệu
                    type: 'GET',
                    data: {
                        range: range // Gửi khoảng thời gian (7 ngày, 30 ngày, 365 ngày, hoặc "all")
                    },
                    success: function(response) {
                        console.log(response);
                        // Cập nhật dữ liệu biểu đồ
                        var activityLabels = response.labels;
                        var activityPostsCounts = response.postsCounts;
                        var activityCommentsCounts = response.commentsCounts;

                        // Cập nhật dữ liệu vào biểu đồ
                        recentActivityChart.data.labels = activityLabels;
                        recentActivityChart.data.datasets[0].data = activityPostsCounts;
                        recentActivityChart.data.datasets[1].data = activityCommentsCounts;
                        recentActivityChart.update(); // Cập nhật biểu đồ
                    }
                });
            }

            // Tải dữ liệu mặc định (7 ngày) khi trang vừa được tải
            loadActivityData(7);
            var ctx = $('#recentActivityChart'); // Giả sử bạn đã có ID của canvas là 'recentActivityChart'
            var recentActivityChart = new Chart(ctx, {
                type: 'line', // Biểu đồ dạng đường
                data: {
                    labels: [], // Nhãn trục X sẽ được cập nhật sau
                    datasets: [{
                            label: 'Số bài viết',
                            data: [], // Dữ liệu bài viết
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Số bình luận',
                            data: [], // Dữ liệu bình luận
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'time', // Trục thời gian
                            time: {
                                unit: 'day', // Mặc định là ngày
                                tooltipFormat: 'MMM d', // Định dạng tooltip
                                displayFormats: {
                                    day: 'MMM d',
                                    week: 'MMM d',
                                    month: 'MMM yyyy'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

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