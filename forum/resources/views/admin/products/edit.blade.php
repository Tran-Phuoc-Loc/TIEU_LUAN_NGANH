@extends('layouts.admin')

@section('content')
    <h1>Chỉnh sửa Sản Phẩm</h1>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Tên Sản Phẩm</label>
            <input type="text" name="name" value="{{ $product->name }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Mô Tả</label>
            <textarea name="description" class="form-control">{{ $product->description }}</textarea>
        </div>

        <div class="form-group">
            <label for="price">Giá</label>
            <input type="number" name="price" value="{{ $product->price }}" class="form-control" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="product_category_id">Danh Mục</label>
            <select name="product_category_id" class="form-control">
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->product_category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="status">Trạng Thái</label>
            <select name="status" class="form-control" required>
                <option value="in_stock" {{ $product->status == 'in_stock' ? 'selected' : '' }}>Còn Hàng</option>
                <option value="out_of_stock" {{ $product->status == 'out_of_stock' ? 'selected' : '' }}>Hết Hàng</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Cập Nhật</button>
    </form>
@endsection
