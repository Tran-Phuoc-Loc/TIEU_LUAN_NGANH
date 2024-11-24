@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục')

@section('content')
    <h1 class="text-center">Quản Lý Danh Mục</h1>
    {{-- Bao gồm phần tìm kiếm --}}
    @include('layouts.partials.search', ['action' => route('admin.product_categories.index'), 'placeholder' => 'Tìm kiếm sản phẩm...'])
    <!-- Phần quản lý danh mục -->
    <div class="mb-5">
        <h2>Danh sách danh mục</h2>
        <a href="{{ route('admin.product_categories.create') }}" class="btn btn-primary mb-3">Thêm Danh Mục</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-primary mb-3">Danh sách Sản Phẩm</a>

        @if(isset($categories) && $categories->isNotEmpty())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Danh Mục</th>
                        <th>Mô tả</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description }}</td>
                        <td>
                            <a href="{{ route('admin.product_categories.edit',  $category) }}" class="btn btn-primary">Sửa</a>
                            <form action="{{ route('admin.product_categories.destroy', $category) }}" method="POST" style="display: inline-block;">
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
                <div>{{ $categories->links() }}</div> <!-- Hiển thị phân trang từ Laravel -->
                <div>Hiển thị {{ $categories->count() }} danh mục trên tổng {{ $categories->total() }}</div>
            </div>
        @else
            <p>Không có danh mục nào.</p>
        @endif
    </div>

@endsection
