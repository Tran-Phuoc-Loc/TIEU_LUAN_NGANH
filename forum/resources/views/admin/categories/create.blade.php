@extends('layouts.admin')

@section('title', 'Thêm Danh mục')

@section('content')
    <h1>Thêm Danh mục</h1>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tên</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control" name="slug" required>
        </div>
        <button type="submit" class="btn btn-success">Lưu</button>
    </form>
@endsection