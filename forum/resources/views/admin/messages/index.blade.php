@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Quản lý Tin nhắn</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Người gửi</th>
                <th>Người nhận</th>
                <th>Sản phẩm</th>
                <th>Ngày gửi</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($messages as $message)
            <tr>
                <td>{{ $message->id }}</td>
                <td>{{ $message->sender->username }}</td>
                <td>{{ $message->receiver->username }}</td>
                <td>{{ $message->product->name }}</td>
                <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge bg-{{ $message->status == 'read' ? 'success' : 'warning' }}">
                        {{ ucfirst($message->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-info btn-sm">Xem</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Hiển thị phân trang -->
    <div class="d-flex justify-content-between">
        <div>{{ $messages->links() }}</div> <!-- Hiển thị phân trang từ Laravel -->
        <div>Hiển thị {{ $messages->count() }} danh mục trên tổng {{ $messages->total() }}</div>
    </div>
</div>
@endsection