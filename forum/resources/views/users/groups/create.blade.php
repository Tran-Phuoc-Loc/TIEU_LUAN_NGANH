@extends('layouts.users')

@section('title', 'Tạo Group')

@section('content')
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
            <h1>Tạo nhóm mới</h1>

            <form action="{{ route('users.groups.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="name">Tên nhóm</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả nhóm</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="avatar">Ảnh đại diện nhóm (Avatar)</label>
                    <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="visibility">Trạng thái nhóm</label>
                    <select name="visibility" id="visibility" class="form-control">
                        <option value="public">Công khai</option>
                        <option value="private">Riêng tư</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="requires_approval">
                        <input type="checkbox" id="requires_approval" name="requires_approval" value="1">
                        Yêu cầu phê duyệt tham gia nhóm
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Tạo nhóm</button>
            </form>
        </div>
    </div>
</div>
@endsection
