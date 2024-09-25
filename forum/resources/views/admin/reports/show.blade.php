@extends('layouts.admin')

@section('title', 'Chi tiết báo cáo')

@section('content')
    <h2>Chi tiết báo cáo</h2>

    <p><strong>Người báo cáo:</strong> {{ $report->user->username }}</p>
    <p><strong>Bài viết bị báo cáo:</strong> {{ $report->post->title }}</p>
    <p><strong>Lý do báo cáo:</strong> {{ $report->reason }}</p>
    <p><strong>Ngày báo cáo:</strong> {{ $report->created_at->format('d/m/Y') }}</p>

    <form action="{{ route('admin.reports.process', $report->id) }}" method="POST">
        @csrf
        <button type="submit" name="action" value="approve" class="btn btn-success">Duyệt và xóa bài viết</button>
        <button type="submit" name="action" value="reject" class="btn btn-danger">Từ chối báo cáo</button>
    </form>
@endsection
