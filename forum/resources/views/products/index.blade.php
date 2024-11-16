@extends('layouts.users')

@section('title', 'Shop')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Menu điều hướng cho màn hình lớn -->
        <div class="col-lg-2 col-md-1 sidebar d-none d-md-block" style="background-color: #fff; position: fixed; height: 100vh; overflow-y: auto;">
            <div class="vertical-navbar">
                <!-- Thông tin người dùng -->
                <div class="user-info text-center mb-4" style="background-color: black;background-image: linear-gradient(135deg, #52545f 0%, #383a45 50%);">
                    @if(auth()->check())
                    <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('storage/images/avataricon.png') }}"
                        alt="Profile picture of {{ auth()->user()->username }}"
                        class="rounded-circle" style="width: 45px; height: 50px;">
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
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="bi bi-pencil"></i>
                                <span class="d-none d-lg-inline">Bài viết của bạn</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">
                                <i class="bi bi-folder"></i>
                                <span class="d-none d-lg-inline">Danh mục</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forums.index') }}">
                                <i class="bi bi-chat-dots"></i>
                                <span class="d-none d-lg-inline">Diễn đàn</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <hr class="my-4">

                <nav class="navbar navbar-dark flex-column">
                    <ul class="navbar-nav">
                        <li class="nav-item" style="padding-bottom: 10px;">
                            <a href="{{ route('products.create') }}" class="btn btn-success">
                                <i class="fas fa-file-pen"></i>
                                <span class="d-none d-lg-inline">Tạo sản phẩm</span>
                            </a>
                        </li>
                        <li class="nav-item" style="padding-bottom: 10px;">
                            <a href="{{ route('users.groups.create') }}" class="btn btn-success">
                                <i class="bi bi-people"></i>
                                <span class="d-none d-lg-inline">Tạo nhóm</span>
                            </a>
                        </li>

                        <!-- Kiểm tra nếu người dùng đã đăng ít nhất 1 sản phẩm -->
                        @auth
                        @if(auth()->user()->products->count() > 0)
                        <li class="nav-item">
                            <a href="{{ route('chat.seller') }}" class="nav-link">Tin nhắn từ khách hàng</a>
                        </li>
                        @endif
                        @endauth

                        <li class="nav-item" style="text-align: center;">
                            @if ($groups->isNotEmpty())
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
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-success w-100 mt-3">Sửa Sản Phẩm</a>
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
        <div class="col-lg-3 col-md-3 mt-lg-0 right-sidebar" style="background-color: #fff; position: fixed; right: 0; height: 100vh; overflow-y: auto;">
            <div class="right-sidebars p-3">
                <h5>Mẹo Vặt Mua Sắm</h5>
                <ul class="list-group list-unstyled mt-3">
                    <!-- Mẹo 1: Cách chọn laptop phù hợp cho sinh viên -->
                    <li class="mb-3">
                        <a href="#collapseExample1" class="text-dark" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseExample1">
                            💡 Cách chọn laptop phù hợp cho sinh viên
                        </a>
                        <div class="collapse" id="collapseExample1">
                            <ul class="list-group list-unstyled mt-2">
                                <li><a class="text-dark" href="#">Nội dung mẹo 1.1</a></li>
                                <li><a class="text-dark" href="#">Nội dung mẹo 1.2</a></li>
                                <li><a class="text-dark" href="#">Nội dung mẹo 1.3</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Mẹo 2: Mẹo tiết kiệm khi mua sách online -->
                    <li class="mb-3">
                        <a href="#collapseExample2" class="text-dark" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseExample2">
                            🔍 Mẹo tiết kiệm khi mua sách online
                        </a>
                        <div class="collapse" id="collapseExample2">
                            <ul class="list-group list-unstyled mt-2">
                                <li><a class="text-dark" href="#">Nội dung mẹo 2.1</a></li>
                                <li><a class="text-dark" href="#">Nội dung mẹo 2.2</a></li>
                                <li><a class="text-dark" href="#">Nội dung mẹo 2.3</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Mẹo 3: Cách tránh lừa đảo khi mua hàng qua mạng -->
                    <li class="mb-3">
                        <a href="#collapseExample3" class="text-dark" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseExample3">
                            🛒 Cách tránh lừa đảo khi mua hàng qua mạng
                        </a>
                        <div class="collapse" id="collapseExample3">
                            <ul class="list-group list-unstyled mt-2">
                                <li><a class="text-dark" href="#">Nội dung mẹo 3.1</a></li>
                                <li><a class="text-dark" href="#">Nội dung mẹo 3.2</a></li>
                                <li><a class="text-dark" href="#">Nội dung mẹo 3.3</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection