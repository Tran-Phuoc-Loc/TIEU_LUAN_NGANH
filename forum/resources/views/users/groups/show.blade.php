@extends('layouts.users')

@section('title', 'Chi tiết nhóm')

@section('content')
<div class="row">
    <div class="post-container">
        <h1>{{ $group->name }}</h1>
        <p>{{ $group->description }}</p>
        <p>Người tạo: {{ $group->creator->username ?? 'Không rõ' }}</p>
        <p>Ngày tạo: {{ $group->created_at->format('d/m/Y H:i') }}</p>

        @php
            $isMember = $group->members()->where('user_id', Auth::id())->exists();
            $hasRequested = $group->memberRequests()->where('user_id', Auth::id())->exists();
        @endphp

        @if(Auth::id() !== $group->creator_id) <!-- Kiểm tra nếu người dùng không phải là người tạo -->
            @if($group->requires_approval)
                @if(!$isMember && !$hasRequested)
                    <form action="{{ route('groups.join', $group->id) }}" method="POST">
                        @csrf
                        <button type="submit">Yêu Cầu Tham Gia Nhóm</button>
                    </form>
                @elseif($hasRequested && $group->memberRequests()->where('user_id', Auth::id())->where('status', 'pending')->exists())
                    <p>Bạn đã yêu cầu tham gia nhóm này. Vui lòng chờ sự phê duyệt từ chủ nhóm.</p>
                @else
                    <p>Bạn đã là thành viên của nhóm này.</p>
                @endif
            @else
                @if(!$isMember)
                    <form action="{{ route('groups.join', $group->id) }}" method="POST">
                        @csrf
                        <button type="submit">Tham Gia Nhóm</button>
                    </form>
                @else
                    <form action="{{ route('groups.leave', $group->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">Rời Nhóm</button>
                    </form>
                    <p>Bạn đã là thành viên của nhóm này.</p>
                @endif
            @endif
        @else
            <p>Xin chào chủ Group</p>
        @endif

        <!-- Kiểm tra nếu người dùng là người tạo nhóm -->
        @if(Auth::id() === $group->creator_id)
            <form action="{{ route('groups.destroy', $group->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')">Xóa Nhóm</button>
            </form>
        @endif

        <!-- Hiển thị danh sách thành viên -->
        <h3>Thành viên trong nhóm:</h3>
        <ul>
            @foreach ($group->members as $user)
                <li>
                    {{ $user->username }}
                    @if(Auth::id() === $group->creator_id && Auth::id() !== $user->id) <!-- Kiểm tra nếu là chủ nhóm và không phải là chính mình -->
                        <form action="{{ route('groups.kick', [$group->id, $user->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn đuổi người này ra khỏi nhóm?')">Kick Rời nhóm</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>

        <!-- Hiển thị yêu cầu tham gia (dành cho chủ nhóm) -->
        @if(Auth::id() === $group->creator_id)
            <h3>Các yêu cầu tham gia:</h3>
            <ul>
                @foreach ($group->joinRequests()->where('status', 'pending')->get() as $request)
                    <li>
                        {{ $request->user->username }}
                        <form action="{{ route('groups.approve', [$group->id, $request->user_id]) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit">Duyệt</button>
                        </form>
                        <form action="{{ route('groups.reject', [$group->id, $request->user_id]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Từ chối</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif

        <!-- Hiển thị bài viết trong nhóm (nếu có) -->
        @if($posts->isNotEmpty())
            <h3>Bài viết trong nhóm:</h3>
            <ul>
                @foreach ($posts as $post)
                    <li>{{ $post->title }}</li>
                @endforeach
            </ul>
        @else
            <p>Nhóm này chưa có bài viết nào.</p>
        @endif
    </div>
</div>
@endsection
