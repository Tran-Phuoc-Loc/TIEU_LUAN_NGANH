@extends('layouts.users')

@section('title', 'Chỉnh sửa nhóm')

@section('content')
@include('layouts.partials.sidebar')

<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
            <h1>Chỉnh sửa nhóm: {{ $group->name }}</h1>

            <form action="{{ route('groups.update', $group->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Tên nhóm</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $group->name }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả nhóm</label>
                    <textarea name="description" id="description" class="form-control">{{ $group->description }}</textarea>
                </div>

                <div class="form-group">
                    <label for="visibility">Trạng thái nhóm</label>
                    <select name="visibility" id="visibility" class="form-control" required>
                        <option value="public" {{ $group->visibility == 'public' ? 'selected' : '' }}>Public</option>
                        <option value="private" {{ $group->visibility == 'private' ? 'selected' : '' }}>Private</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="avatar">Avatar</label>
                    <input type="file" name="avatar" id="avatar" class="form-control">
                    @if ($group->avatar)
                        <img src="{{ asset('storage/' . $group->avatar) }}" alt="Avatar" class="mt-2" style="width: 100px;">
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">Cập nhật nhóm</button>
            </form>
        </div>
    </div>
</div>
@endsection
