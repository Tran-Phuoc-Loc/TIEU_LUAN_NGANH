@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h2>Thêm Danh mục Sản phẩm mới</h2>

        <form action="{{ route('admin.product_categories.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Tên Danh mục</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Nhập tên danh mục" required>
                @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" id="description" rows="4" placeholder="Nhập mô tả"></textarea>
                @error('description')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Thêm Danh mục</button>
            <a href="{{ route('admin.product_categories.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
@endsection
