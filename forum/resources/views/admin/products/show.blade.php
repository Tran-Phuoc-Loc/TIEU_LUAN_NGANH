@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Chi tiết sản phẩm</h1>
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <!-- Thông tin sản phẩm -->
            <div class="row">
                <!-- Hình ảnh chính -->
                <div class="col-md-4">
                    <h4 class="text-muted">Hình ảnh chính</h4>
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded border mb-3" style="max-height: 300px;">
                    @else
                        <p class="text-muted">Không có hình ảnh chính.</p>
                    @endif
                </div>

                <!-- Thông tin chi tiết -->
                <div class="col-md-8">
                    <h3 class="fw-bold">{{ $product->name }}</h3>
                    <p><strong>Người dùng:</strong> {{ $product->user->username ?? 'N/A' }}</p>
                    <p><strong>Danh mục:</strong> {{ $product->category->name ?? 'N/A' }}</p>
                    <p><strong>Mô tả:</strong></p>
                    <p class="text-muted">{{ $product->description }}</p>
                    <p><strong>Giá:</strong> 
                        <span class="text-danger fw-bold">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                    </p>
                    <p><strong>Trạng thái:</strong> 
                        @if ($product->status === 'in_stock')
                            <span class="badge bg-success">Còn hàng</span>
                        @else
                            <span class="badge bg-danger">Hết hàng</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Hình ảnh phụ -->
            <div class="mt-4">
                <h4 class="text-muted">Hình ảnh phụ</h4>
                <div class="d-flex flex-wrap">
                    @forelse ($product->images as $image)
                        <div class="me-2 mb-2">
                            <img src="{{ asset('storage/' . $image->image) }}" alt="Ảnh phụ" class="img-thumbnail rounded" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                    @empty
                        <p class="text-muted">Không có ảnh phụ.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Hành động quản trị -->
        <div class="card-footer text-end bg-light">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary me-2">Chỉnh sửa</a>
            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">Xóa sản phẩm</button>
            </form>
        </div>
    </div>
</div>
@endsection