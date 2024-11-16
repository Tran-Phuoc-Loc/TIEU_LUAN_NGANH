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

        <div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
            <div class="post-container mb-4">
                <div class="row">
                    <!-- Cột ảnh sản phẩm chính -->
                    <div class="col-lg-6 col-md-6">
                        <!-- Bootstrap Carousel cho các hình ảnh lớn -->
                        @if($product->images->count() > 0)
                        <div id="productImagesCarousel" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner">
                                <!-- Ảnh đại diện chính -->
                                <div class="carousel-item active">
                                    <img
                                        src="{{ asset('storage/' . $product->image) }}"
                                        alt="{{ $product->name }}"
                                        class="img-fluid d-block w-100"
                                        style="height: 400px; object-fit: cover; border-radius: 8px;">
                                </div>
                                <!-- Các ảnh khác -->
                                @foreach ($product->images as $index => $image)
                                <div class="carousel-item">
                                    <img
                                        src="{{ asset('storage/' . $image->image) }}"
                                        alt="Additional Image"
                                        class="img-fluid d-block w-100"
                                        style="height: 400px; object-fit: cover; border-radius: 8px;">
                                </div>
                                @endforeach
                            </div>
                            <!-- Nút điều hướng carousel -->
                            <button class="carousel-control-prev" type="button" data-bs-target="#productImagesCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productImagesCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        @endif

                        <!-- Hàng ảnh nhỏ bên dưới -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex overflow-auto" style="gap: 10px; max-width: 100%; padding-bottom: 10px;">
                                    <!-- Ảnh đại diện chính -->
                                    <div>
                                        <img
                                            src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name }}"
                                            class="img-fluid"
                                            style="cursor: pointer; width: 100px; height: 100px; object-fit: cover;"
                                            data-index="0"
                                            onclick="changeSlide(this)">
                                    </div>

                                    <!-- Các ảnh khác -->
                                    @foreach ($product->images as $index => $image)
                                    <div>
                                        <img
                                            src="{{ asset('storage/' . $image->image) }}"
                                            alt="Thumbnail"
                                            class="img-fluid"
                                            style="cursor: pointer; width: 100px; height: 100px; object-fit: cover;"
                                            data-index="{{ $index + 1 }}"
                                            onclick="changeSlide(this)">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cột chi tiết sản phẩm -->
                    <div class="col-lg-6 col-md-6">
                        <h2 class="product-name">{{ $product->name }}</h2>

                        <!-- Hiển thị trạng thái sản phẩm -->
                        <p>
                            <strong>Trạng thái:</strong>
                            @if($product->status === 'in_stock')
                            <span class="text-success">Còn hàng</span>
                            @else
                            <span class="text-danger">Hết hàng</span>
                            @endif
                        </p>

                        <!-- Hiển thị giá -->
                        <h4 style="color: #ff6a00;">{{ number_format($product->price, 0, ',', '.') }} VND</h4>

                        <!-- Mô tả sản phẩm -->
                        <p>{{ $product->description }}</p>

                        <!-- Hiển thị người bán -->
                        <p><strong>Người bán:</strong> {{ $product->user->username ?? 'Không rõ' }}</p>

                        <!-- Nút liên hệ người bán -->
                        @auth
                        @if(auth()->id() !== $product->user_id)
                        <a href="{{ route('chat.product', ['productId' => $product->id, 'receiverId' => $product->user->id]) }}" class="btn btn-primary mt-3">Nhắn tin với người bán</a>
                        @else
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-success mt-3">Sửa sản phẩm</a>
                        @endif
                        @else
                        <a href="{{ route('login') }}" class="btn btn-warning mt-3">Đăng nhập để liên hệ</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hàm thay đổi slide khi nhấp vào ảnh thu nhỏ
            window.changeSlide = function(element) {
                const index = element.getAttribute('data-index');
                const carouselElement = document.getElementById('productImagesCarousel');
                const carousel = bootstrap.Carousel.getOrCreateInstance(carouselElement);

                // Chuyển đến slide có chỉ số tương ứng
                carousel.to(parseInt(index));
            };
        });
    </script>


    @endsection