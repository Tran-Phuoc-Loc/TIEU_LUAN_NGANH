@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Chi tiết nhóm: {{ $group->name }}</h2>
    <p><strong>Mô tả:</strong> {{ $group->description }}</p>
    <p><strong>Chủ nhóm:</strong> {{ $group->creator->username ?? 'Không rõ' }}</p>

    <h3>Thành viên của nhóm</h3>
    <ul>
        @foreach ($group->members as $member)
            <li>{{ $member->username }} ({{ $member->email }})</li>
        @endforeach
    </ul>

    <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection
