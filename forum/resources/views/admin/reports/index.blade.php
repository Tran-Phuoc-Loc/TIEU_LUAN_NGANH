@extends('layouts.admin')

@section('title', 'Danh sách báo cáo')

@section('content')
<style>
    .table th {
        background-color: #000 !important;
        color: #fff !important;
    }
</style>

<div class="container">
    <h2 class="my-4 text-center">Danh sách báo cáo vi phạm</h2>
    {{-- Bao gồm phần tìm kiếm --}}
    @include('layouts.partials.search', ['action' => route('admin.reports.index'), 'placeholder' => 'Tìm kiếm sản phẩm...'])
    <!-- Bảng danh sách báo cáo -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-dark">
                <tr class="bg-dark text-light">
                    <th>Người báo cáo</th>
                    <th>Bài viết</th>
                    <th>Lý do</th>
                    <th>Ngày báo cáo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                <tr>
                    <td>
                        <span class="badge bg-info text-dark">{{ $report->user->username }}</span>
                    </td>
                    <td>{{ $report->post->title }}</td>
                    <td>{{ $report->reason }}</td>
                    <td>{{ $report->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-muted">Không có báo cáo nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-4">
        {{ $reports->links() }}
    </div>
    @endsection