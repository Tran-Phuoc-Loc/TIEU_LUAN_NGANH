@extends('layouts.admin')

@section('content')
    <h1>Chỉnh sửa danh mục</h1>

    <form action="{{ route('admin.product_categories.update', $product_category) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Tên Danh Mục</label>
            <input type="text" name="name" value="{{ $product_category->name }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Mô Tả</label>
            <textarea name="description" class="form-control">{{ $product_category->description }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Cập Nhật</button>
    </form>
@endsection
