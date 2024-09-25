@extends('layouts.admin')

@section('title', 'Sửa Danh mục')

@section('content')
    <h1>Sửa Danh mục</h1>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Tên</label>
            <input type="text" class="form-control" name="name" value="{{ $category->name }}" required>
        </div>
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control" name="slug" value="{{ $category->slug }}" required>
        </div>
        <button type="submit" class="btn btn-success">Cập nhật</button>
    </form>
@endsection