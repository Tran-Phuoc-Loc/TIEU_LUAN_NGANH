@extends('layouts.admin')

@section('title', 'Quản lý danh mục')

@section('content')
<div class="container">
    <h1>Quản lý danh mục</h1>
    <a href="{{ route('admin.forum.categories.create') }}" class="btn btn-primary">Thêm danh mục</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{!! Str::limit(strip_tags($category->description), 100) !!}</td>
                    <td>
                        <a href="{{ route('admin.forum.categories.edit', $category->id) }}" class="btn btn-warning">Sửa</a>
                        <form action="{{ route('admin.forum.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
