@extends('layouts.users')

@section('title', 'Các Nhóm Tôi Tham Gia')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="group-container">
            <h1 class="text-center">Các Nhóm Tôi Tham Gia</h1>

            @if($groups->isNotEmpty())
            <ul class="list-group">
                @foreach ($groups as $group)
                <li class="group-item d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('users.groups.show', $group->id) }}" class="group-link">
                            <strong>{{ $group->name }}</strong>
                        </a>
                        <br>
                        <small class="text-muted">Tạo bởi: {{ $group->creator->username }}</small>
                    </div>

                    <div>
                        @if(Auth::id() === $group->creator_id)
                        <form action="{{ route('groups.destroy', $group->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-button" onclick="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')">Xóa</button>
                        </form>
                        @else
                        <form action="{{ route('groups.join', $group->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">Tham Gia</button>
                        </form>
                        @endif
                    </div>
                </li>

                @if($group->memberRequests->isNotEmpty())
                <div class="mt-3 p-3 bg-light">
                    <h5 class="text-danger">Yêu cầu tham gia nhóm {{ $group->name }}:</h5>
                    <ul class="list-group">
                        @foreach ($group->memberRequests as $request)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $request->user->username }}
                            <form action="{{ route('groups.approve', $group->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $request->user_id }}">
                                <button type="submit" class="btn btn-success btn-sm">Chấp Nhận</button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @else
                <p class="text-muted">Không có yêu cầu tham gia nào cho nhóm {{ $group->name }}.</p>
                @endif

                @endforeach
            </ul>
            @else
            <div class="empty-group-message">
                Bạn chưa tham gia nhóm nào.
            </div>
            @endif
        </div>
    </div>
</div>
<style>
    .group-container {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .group-item {
        padding: 15px;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        margin-bottom: 15px;
        transition: box-shadow 0.3s ease;
    }

    .group-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .group-link {
        font-size: 1.2em;
        color: #007bff;
        text-decoration: none;
    }

    .group-link:hover {
        text-decoration: underline;
    }

    .delete-button {
        background-color: #ff4d4f;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .delete-button:hover {
        background-color: #ff7875;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .empty-group-message {
        background-color: #e9ecef;
        padding: 15px;
        border-radius: 5px;
        text-align: center;
        font-size: 1.1em;
        color: #6c757d;
    }
</style>
@endsection