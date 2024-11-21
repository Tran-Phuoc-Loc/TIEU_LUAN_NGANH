@extends('layouts.users')

@section('title', 'Quản Lý Sản Phẩm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Menu điều hướng cho màn hình lớn -->
        <div class="col-lg-2 col-md-1 sidebar d-none d-md-block" style="background-color: #fff; position: fixed; height: 100vh; overflow-y: auto;">
            <div class="vertical-navbar">
                <!-- Thông tin người dùng -->
                <div class="user-info text-center mb-4" style="background-color: black;background-image: linear-gradient(135deg, #52545f 0%, #383a45 50%);">
                @if(Auth::check() && Auth::user()->profile_picture)
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
                            <a href="{{ route('chat.seller') }}" class="nav-link"><i class="bi bi-messenger"></i>Khách hàng</a>
                        </li>
                        @endif
                        @endauth

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
            <h1>Quản lý sản phẩm</h1>

            <table class="table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Ảnh</th>
                        <th>Ngày tạo</th>
                        <th>Ngày cập nhật</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ optional($product->category)->name }}</td>
                        <td>{{ number_format($product->price, 2) }} VND</td>
                        <td>{{ ucfirst($product->status) }}</td>
                        <td>
                            @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" width="50" height="50" alt="product image">
                            @else
                            Chưa có ảnh
                            @endif
                        </td>
                        <td>{{ $product->created_at->format('d-m-Y H:i:s') }}</td>
                        <td>{{ $product->updated_at->format('d-m-Y H:i:s') }}</td>
                        <td>
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-primary">Chỉnh sửa</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @endsection