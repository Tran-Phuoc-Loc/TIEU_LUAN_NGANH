@extends('layouts.admin')

@section('title', 'Quản Lý Sản Phẩm')

@section('content')
<h1 class="text-center">Quản Lý Sản Phẩm</h1>
{{-- Bao gồm phần tìm kiếm --}}
@include('layouts.partials.search', ['action' => route('admin.products.index'), 'placeholder' => 'Tìm kiếm sản phẩm...'])

<!-- Phần quản lý sản phẩm -->
<div>
    <h2>Danh sách sản phẩm</h2>
    @if(isset($products) && $products->isNotEmpty())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Giá</th>
                    <th>Danh Mục</th>
                    <th>Trạng Thái</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ number_format($product->price, 0, ',', '.') }} VNĐ</td> <!-- Định dạng giá -->
                    <td>{{ $product->category->name ?? 'Không xác định' }}</td>
                    <td>{{ $product->status }}</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">Sửa</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Hiển thị phân trang -->
        <div class="d-flex justify-content-between">
            <div>{{ $products->links() }}</div> <!-- Hiển thị liên kết phân trang từ Laravel -->
            <div>Hiển thị {{ $products->count() }} sản phẩm trên tổng {{ $products->total() }}</div>
        </div>

    @else
        <p>Không có sản phẩm nào.</p>
    @endif
</div>
@endsection
