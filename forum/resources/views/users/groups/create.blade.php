@extends('layouts.users')

@section('title', 'Tạo Group')

@section('content')
<div class="row">
    <div class="post-container">
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

            <label for="requires_approval">
                <input type="checkbox" id="requires_approval" name="requires_approval" value="1">
                Yêu cầu phê duyệt tham gia nhóm
            </label>

            <button type="submit" class="btn btn-primary">Tạo nhóm</button>
        </form>
    </div>
</div>
@endsection