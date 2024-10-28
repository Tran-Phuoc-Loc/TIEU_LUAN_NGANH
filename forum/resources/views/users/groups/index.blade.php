@extends('layouts.users')

@section('title', 'Các Nhóm Tôi Tham Gia')

@section('content')
<style>
    .group-container {
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    .group-item {
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }

    .group-link {
        font-size: 1.2rem;
        color: #007bff;
        text-decoration: none;
    }

    .group-link:hover {
        text-decoration: underline;
    }

    .delete-button {
        color: #ff4d4d;
        background: none;
        border: none;
        font-weight: bold;
        cursor: pointer;
    }

    .request-list {
        margin-top: 10px;
        background-color: #f1f1f1;
        padding: 10px;
        border-radius: 5px;
    }

    .empty-group-message {
        text-align: center;
        padding: 30px;
        color: #777;
        font-size: 1.1rem;
    }
</style>

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
                            <!-- Nút Xóa nếu là người tạo nhóm -->
                            <form action="{{ route('groups.destroy', $group->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-button" onclick="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')">Xóa</button>
                            </form>
                        @elseif($group->hasMember(Auth::id()))
                            <!-- Thông báo nếu người dùng đã là thành viên -->
                            <span class="text-success">Bạn đã là thành viên</span>
                        @elseif($group->hasJoinRequest(Auth::id()))
                            <!-- Thông báo nếu đã gửi yêu cầu tham gia -->
                            <span class="text-warning">Bạn đã yêu cầu tham gia</span>
                        @else
                            <!-- Nút Tham gia nếu chưa phải thành viên và chưa yêu cầu tham gia -->
                            <form action="{{ route('groups.join', $group->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Tham Gia</button>
                            </form>
                        @endif
                    </div>
                </li>

                <!-- Hiển thị yêu cầu tham gia nếu có -->
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
@endsection