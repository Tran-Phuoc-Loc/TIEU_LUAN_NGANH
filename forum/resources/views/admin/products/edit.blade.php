@extends('layouts.admin')

@section('content')
<h1>Chỉnh Sửa Sản Phẩm</h1>

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
        <input
            type="number"
            name="price"
            value="{{ number_format($product->price, 0, '.', '') }}"
            class="form-control"
            step="0.01"
            required>
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

    <div class="form-group">
        <label for="image">Hình Ảnh Chính</label>
        @if($product->image)
        <div class="mb-3">
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid" style="max-height: 200px;" loading="lazy">
        </div>
        @endif
        <input type="file" name="image" class="form-control">
    </div>

    <div class="form-group">
        <label for="additional_images">Hình Ảnh Phụ</label>
        @if($product->images && $product->images->count() > 0)
        <div class="d-flex flex-wrap mb-3">
            @foreach($product->images as $image)
            <div class="d-flex align-items-center mb-2 me-2">
                <img src="{{ asset('storage/' . $image->image) }}" alt="Ảnh phụ" class="img-fluid" style="max-height: 100px;" loading="lazy">
            </div>
            @endforeach
        </div>
        @endif
        <input type="file" name="additional_images[]" class="form-control" multiple>
    </div>

    <button type="submit" class="btn btn-success mt-3">Cập Nhật</button>
</form>
@endsection