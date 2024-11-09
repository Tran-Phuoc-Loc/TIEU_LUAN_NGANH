@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Chi tiết nhóm: {{ $group->name }}</h2>
    <p><strong>Mô tả:</strong> {{ $group->description }}</p>
    <p><strong>Chủ nhóm:</strong> {{ $group->creator->username ?? 'Không rõ' }}</p>

    <h3>Thống kê nhóm</h3>
    <ul>
        <li><strong>Số lượng thành viên:</strong> {{ $group->members->count() }}</li>
        <li><strong>Số lượng tin nhắn:</strong> {{ $group->chats_count }}</li>
        <li><strong>Ngày tạo nhóm:</strong> {{ $group->created_at->format('d/m/Y') }}</li>
        <li><strong>Ngày hoạt động gần nhất:</strong> {{ $group->chats()->latest()->first()?->created_at->format('d/m/Y H:i') ?? 'Chưa có hoạt động' }}</li>
    </ul>

    <h3>Thành viên của nhóm</h3>
    <ul>
        @foreach ($group->members as $member)
            <li>{{ $member->username }} ({{ $member->email }})</li>
        @endforeach
    </ul>

    <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection
