@extends('layouts.admin')

@section('title', 'Danh sách báo cáo')

@section('content')
    <h2>Danh sách báo cáo vi phạm</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Người báo cáo</th>
                <th>Bài viết</th>
                <th>Lý do</th>
                <th>Ngày báo cáo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ $report->user->username }}</td>
                    <td>{{ $report->post->title }}</td>
                    <td>{{ $report->reason }}</td>
                    <td>{{ $report->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-primary">Xem chi tiết</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $reports->links() }} <!-- Phân trang -->
@endsection
