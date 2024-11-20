@extends('layouts.users')

@section('title', 'Shop')

<style>
    /* Ẩn các ảnh sau ảnh thứ 2 */
    .image-grid .image-item:nth-child(n+3) {
        display: none;
    }

    .post-images-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .image-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .image-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        width: 100%;
        aspect-ratio: 1;
        /* Khung hình vuông */
    }

    .image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        border-radius: 8px;
    }

    /* Hiển thị số lượng ảnh còn lại */
    .more-images-overlay {
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: rgba(0, 0, 0, 0.6);
        color: #fff;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
</style>
@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Menu điều hướng cho màn hình lớn -->
        <div class="col-lg-2 col-md-1 sidebar d-none d-md-block" style="background-color: #fff; position: fixed; height: 100vh; overflow-y: auto;">
            <div class="vertical-navbar">
                <!-- Thông tin người dùng -->
                <div class="user-info text-center mb-4" style="background-color: black; background-image: linear-gradient(135deg, #52545f 0%, #383a45 50%);">
                    @if(Auth::user()->profile_picture)
                    <a class="dropdown-item" href="{{ route('users.profile.index', Auth::user()->id) }}">
                        <!-- Kiểm tra nếu profile_picture là URL hợp lệ, nếu không thì lấy ảnh trong storage -->
                        <img src="{{ 
                    (filter_var(auth()->user()->profile_picture, FILTER_VALIDATE_URL)) 
                    ? auth()->user()->profile_picture 
                    : (auth()->user()->profile_picture 
                        ? asset('storage/' . auth()->user()->profile_picture) 
                        : asset('storage/images/avataricon.png')) 
                }}"
                            alt="Profile picture of {{ auth()->user()->username }}"
                            class="rounded-circle" style="width: 45px; height: 50px;">
                    </a>
                    <h5 class="d-none d-md-block" style="color: #fff;">{{ auth()->user()->username }}</h5>
                    <hr style="border-top: 1px solid black; margin: 10px 0;">
                    @endif
                </div>

                <nav class="navbar navbar-dark flex-column">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">
                                <i class="fas fa-house"></i>
                                <span class="d-none d-lg-inline">Trang chủ</span>
                            </a>
                        </li>
                        @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index', ['user_posts' => 'true']) }}">
                                <i class="bi bi-pencil"></i>
                                <span class="d-none d-lg-inline">Bài viết của bạn</span>
                            </a>
                        </li>
                        @endauth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">
                                <i class="bi bi-folder"></i>
                                <span class="d-none d-lg-inline">Danh mục</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forums.index') }}">
                                <i class="bi bi-wechat"></i>
                                <span class="d-none d-lg-inline">Diễn đàn</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.groups.index') }}">
                                <i class="bi bi-people"></i>
                                <span class="d-none d-lg-inline">Nhóm tham gia</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <hr class="my-4">

                <nav class="navbar navbar-dark flex-column">
                    <ul class="navbar-nav">
                        <li class="nav-item" style="padding-bottom: 10px;">
                            <a href="{{ route('users.posts.create') }}" class="btn btn-success">
                                <i class="fas fa-file-pen"></i>
                                <span class="d-none d-lg-inline">Viết bài</span>
                            </a>
                        </li>
                        <li class="nav-item" style="padding-bottom: 10px;">
                            <a href="{{ route('users.groups.create') }}" class="btn btn-success">
                                <i class="bi bi-people"></i>
                                <span class="d-none d-lg-inline">Tạo nhóm</span>
                            </a>
                        </li>
                        <li class="nav-item" style="text-align: center;">
                            @if (isset($groups) && $groups->isNotEmpty())
                            @php $firstGroup = $groups->first(); @endphp
                            <a href="{{ route('groups.chat', $firstGroup->id) }}">
                                <i class="fas fa-comment-sms" style="font-size: 40px"></i>
                            </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="col-lg-7 col-md-7 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
            <!-- Form lọc sản phẩm -->
            <form method="GET" action="{{ route('products.index') }}">
                <div class="d-flex justify-content-between">
                    <select name="sort_by" class="form-select w-25">
                        <option value="">Lọc theo</option>
                        <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>A-Z</option>
                        <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Z-A</option>
                        <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                        <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Sản phẩm mới</option>
                    </select>

                    <button type="submit" class="btn btn-primary">Lọc</button>
                </div>
            </form>

            <div class="post-container mb-4">
                <div class="row">
                    @foreach($products as $product)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="post-container" style="border: 1px solid #c8ccd0; background-color:#fff; padding: 20px; height:500px;">

                            <!-- Hiển thị nhãn "New" nếu sản phẩm được đăng trong vòng 7 ngày -->
                            @if(\Carbon\Carbon::parse($product->created_at)->setTimezone('Asia/Ho_Chi_Minh')->greaterThanOrEqualTo(\Carbon\Carbon::now('Asia/Ho_Chi_Minh')->subDays(7)))
                            <div class="position-absolute bg-danger text-white p-1 fw-bold" style="transform: rotate(20deg);">
                                New
                            </div>
                            @endif

                            <!-- Hiển thị ảnh sản phẩm với kích thước cố định và cắt vừa khung -->
                            <img
                                src="{{ asset('storage/' . $product->image) }}"
                                alt="Product Image"
                                class="img-fluid my-3"
                                style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">

                            <!-- Thông tin sản phẩm -->
                            <div class="product-details">
                                <!-- Trạng thái sản phẩm -->
                                <h7>
                                    @if($product->status === 'in_stock')
                                    Còn hàng
                                    @else
                                    Hết hàng
                                    @endif
                                </h7>
                                <!-- Hiển thị tên sản phẩm -->
                                <a href="{{ route('products.show', ['product' => $product->id]) }}">
                                    <h5 class="product-name" style="height: 70px;">{{ Str::limit($product->name, 30) }}</h5>
                                </a>

                                <!-- Hiển thị giá sản phẩm -->
                                <p class="product-price" style="color: #ff6a00; font-weight: bold;">
                                    Giá: {{ number_format($product->price, 0, ',', '.') }} VND
                                </p>

                                <!-- Phần thông tin người bán và nút liên hệ -->
                                <div class="mt-auto">
                                    @auth
                                    @if(auth()->id() !== $product->user_id)
                                    <!-- Nút nhắn tin với người bán -->
                                    <a href="{{ route('chat.product', ['productId' => $product->id, 'receiverId' => $product->user->id]) }}" class="btn btn-primary w-100 mt-3">Nhắn tin với người bán</a>
                                    @else
                                    <!-- Nút chỉnh sửa sản phẩm cho người bán -->
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-edit-product">Sửa Sản Phẩm</a>
                                    @endif
                                    @else
                                    <!-- Nút đăng nhập -->
                                    <a href="{{ route('login') }}" class="btn btn-warning w-100 mt-3">Đăng nhập để liên hệ</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Phân trang -->
            @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
            @endif
        </div>

        <!-- Sidebar phải: Mẹo Vặt -->
        <div class="col-lg-3 col-md-3 mt-lg-0 right-sidebar bg-light" style="position: fixed; right: 0; height: 100vh; overflow-y: auto;">
            <div class="right-sidebars p-4 shadow-sm">
                <h5 class="fw-bold text-primary">Mẹo Vặt Sản Phẩm</h5>
                <p class="text-muted">Cập nhật những mẹo hay giúp bạn mua sắm thông minh và hiệu quả!</p>
                <p class="text-muted">Cẩn thận khi mua hàng qua mạng⚠️</p>
                <ul class="list-group list-unstyled mt-4">
                    <!-- Mẹo 1: Cách chọn laptop phù hợp cho sinh viên -->
                    <li class="mb-4">
                        <a href="#collapseLaptopTips" class="text-dark fw-bold d-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseLaptopTips">
                            <i class="bi bi-laptop me-2 text-primary"></i> Cách chọn laptop phù hợp cho sinh viên
                        </a>
                        <div class="collapse mt-2" id="collapseLaptopTips">
                            <ul class="list-group list-unstyled ps-3">
                                <li><a class="text-secondary" href="#">✔️ Tìm laptop theo ngân sách</a></li>
                                <li><a class="text-secondary" href="#">✔️ Cấu hình phù hợp với ngành học</a></li>
                                <li><a class="text-secondary" href="#">✔️ Cần lựa chọn người bán uy tính</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Mẹo 2: Cách tránh lừa đảo khi mua hàng qua mạng -->
                    <li class="mb-4">
                        <a href="#collapseFraudTips" class="text-dark fw-bold d-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseFraudTips">
                            <i class="bi bi-shield-check me-2 text-danger"></i> Cách tránh lừa đảo khi mua hàng qua mạng
                        </a>
                        <div class="collapse mt-2" id="collapseFraudTips">
                            <ul class="list-group list-unstyled ps-3">
                                <li><a class="text-secondary" href="#">✔️ Xem người bán cẩn thận trước khi mua hàng</a></li>
                                <li><a class="text-secondary" href="#">✔️ Không thanh toán trước khi nhận hàng</a></li>
                                <li><a class="text-secondary" href="#">✔️ Kiểm tra thông tin người bán</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
                <div class="mt-4">
                    <a href="{{ route('product.management') }}" class="btn btn-primary w-100">Quản lý sản phẩm</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection