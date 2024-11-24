@extends('layouts.admin')

@section('title', 'Sửa danh mục')

@section('content')
<div class="container">
    <h1>Sửa danh mục</h1>
    <form action="{{ route('admin.forum.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group mb-3">
            <label for="name">Tên danh mục</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" required>
        </div>
        <div class="form-group mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control" name="description"
                style="height: 135px;"
                required>{{ $category->description }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Cập nhật</button>
    </form>
</div>
@endsection