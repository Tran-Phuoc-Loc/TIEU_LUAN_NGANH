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
                                <!-- Nếu là người tạo nhóm, hiển thị nút Xóa -->
                                @if(Auth::id() === $group->creator_id)
                                    <form action="{{ route('groups.destroy', $group->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-button" onclick="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')">Xóa</button>
                                    </form>
                                
                                <!-- Nếu người dùng là thành viên, hiển thị thông báo -->
                                @elseif($group->isMember(Auth::user()))
                                    <a href="{{ route('groups.chat', $group->id) }}" class="btn btn-primary btn-sm">Vào nhóm</a>
                                
                                <!-- Nếu người dùng đã gửi yêu cầu tham gia, hiển thị thông báo -->
                                @elseif($group->hasJoinRequest(Auth::user()))
                                    <span class="text-warning">Bạn đã yêu cầu tham gia</span>
                                
                                <!-- Nếu người dùng không phải thành viên và chưa yêu cầu tham gia, hiển thị nút Tham Gia -->
                                @else
                                    <form action="{{ route('groups.join', $group->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Tham Gia</button>
                                    </form>
                                @endif
                            </div>
                        </li>
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
