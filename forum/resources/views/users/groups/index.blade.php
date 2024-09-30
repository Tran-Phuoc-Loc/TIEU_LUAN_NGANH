@extends('layouts.users')

@section('title', 'Các Nhóm Tôi Tham Gia')

@section('content')
<div class="welcome-contents">
    <h1>Các Nhóm Tôi Tham Gia</h1>
    @if($groups->isNotEmpty())
        <ul>
            @foreach ($groups as $group)
                <li>
                    <a href="{{ route('users.groups.show', $group->id) }}">
                        {{ $group->name }}
                        <span style="font-size: smaller; color: gray;">(Tạo bởi: {{ $group->creator->username }})</span>
                    </a>
                    @if(Auth::id() === $group->creator_id)
                        <form action="{{ route('groups.destroy', $group->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')">Xóa Nhóm</button>
                        </form>
                    @else
                        <form action="{{ route('groups.join', $group->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit">Tham Gia Nhóm</button>
                        </form>
                    @endif
                </li>

                @if($group->memberRequests->isNotEmpty())
                    <h3 style="font-size: 1.2em; color: red;">Yêu cầu tham gia nhóm {{ $group->name }}:</h3>
                    <ul>
                        @foreach ($group->memberRequests as $request)
                            <li>
                                {{ $request->user->username }}
                                <form action="{{ route('groups.approve', $group->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $request->user_id }}">
                                    <button type="submit">Chấp Nhận</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>Không có yêu cầu tham gia nào cho nhóm {{ $group->name }}.</p>
                @endif
            @endforeach
        </ul>
    @else
        <p>Bạn chưa tham gia nhóm nào.</p>
    @endif
</div>
@endsection