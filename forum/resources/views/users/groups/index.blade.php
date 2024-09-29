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
                        {{-- Or use ID: (ID: {{ $group->id }}) --}}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>Bạn chưa tham gia nhóm nào.</p>
    @endif
</div>
@endsection
