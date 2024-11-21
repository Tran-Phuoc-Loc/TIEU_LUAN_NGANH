@extends('layouts.users')

@section('title', 'Chi tiết sản phẩm')

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
                @if(Auth::check() && Auth::user()->profile_picture)
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
                                        style="object-fit: cover; border-radius: 8px;">
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
                        @else
                        <!-- Nếu chỉ có một ảnh, hiển thị ảnh chính -->
                        <div class="carousel-item active" style="width:60%;">
                            <img
                                src="{{ asset('storage/' . $product->image) }}"
                                alt="{{ $product->name }}"
                                class="img-fluid d-block w-100"
                                style="object-fit: cover; border-radius: 8px;">
                        </div>
                        @endif

                        <!-- Hàng ảnh nhỏ bên dưới -->
                        @if($product->images->count() > 1)
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
                        @endif
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