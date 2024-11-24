@extends('layouts.admin')

@section('title', 'Thêm danh mục')

@section('content')
<div class="container">
    <h1>Thêm danh mục</h1>
    <form action="{{ route('admin.forum.categories.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="name">Tên danh mục</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control" name="description"
                style="height: 135px;"
                required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Lưu</button>
    </form>
</div>
@endsection
