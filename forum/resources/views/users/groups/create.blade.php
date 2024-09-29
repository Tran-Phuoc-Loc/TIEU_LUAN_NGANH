@extends('layouts.users')

@section('title', 'Tạo Group')

@section('content')
<div class="welcome-contents">
    <h1>Tạo nhóm mới</h1>

    <form action="{{ route('users.groups.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Tên nhóm</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Mô tả nhóm</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Tạo nhóm</button>
    </form>
</div>
@endsection
