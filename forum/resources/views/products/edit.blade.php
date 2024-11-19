@extends('layouts.users')

@section('title', 'Thay đổi Sản Phẩm')

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
                            <a href="{{ route('products.index') }}" class="btn btn-success">
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
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="col-lg-7 col-md-7 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
            <div class="post-container mb-4">
                <h2 class="text-center mb-4">Chỉnh sửa sản phẩm: {{ $product->name }}</h2>

                <!-- Hiển thị thông báo nếu có -->
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Form chỉnh sửa sản phẩm -->
                <form action="{{ route('products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Danh mục -->
                    <div class="mb-3">
                        <label for="product_category_id" class="form-label">Danh mục</label>
                        <select name="product_category_id" class="form-select" id="product_category_id" required>
                            <option value="">Chọn danh mục</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $category->id == $product->product_category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('product_category_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tên sản phẩm -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên Sản Phẩm</label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mô tả sản phẩm -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô Tả Sản Phẩm</label>
                        <textarea name="description" class="form-control" id="description" rows="5" required>{{ old('description', $product->description) }}</textarea>
                        @error('description')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image">Ảnh đại diện</label>
                        <input type="file" class="form-control" name="image">
                        <div class="current-images d-flex flex-wrap">
                            <div class="image-wrapper me-3 mb-3" style="position: relative; width: 100px; height: 100px;">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" style="width: 100%; height: 100%; object-fit: cover; border: 1px solid #ddd;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="images">Hình ảnh khác</label>
                        <input type="file" class="form-control" name="images[]" multiple>
                        <div class="current-images d-flex flex-wrap">
                            @foreach ($product->images as $image)
                            <div class="image-wrapper me-3 mb-3" style="position: relative; width: 100px; height: 100px;">
                                <img src="{{ asset('storage/' . $image->image) }}" alt="Product Image" style="width: 100%; height: 100%; object-fit: cover; border: 1px solid #ddd;">
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Trường Status -->
                    <div class="form-group mb-3">
                        <label for="status">Trạng thái</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="in_stock" {{ $product->status == 'in_stock' ? 'selected' : '' }}>Còn hàng</option>
                            <option value="out_of_stock" {{ $product->status == 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                        </select>
                        @error('status')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Giá sản phẩm -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Giá</label>
                        <input type="number" name="price" class="form-control" id="price" value="{{ old('price', $product->price) }}" required>
                        @error('price')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Cập nhật sản phẩm</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
                </form>
                <!-- Nút xóa sản phẩm -->
                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">Xóa sản phẩm</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection