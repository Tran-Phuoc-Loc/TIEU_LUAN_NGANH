@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Chi tiết Tin nhắn</h1>

    <div class="card">
        <div class="card-header">
            Tin nhắn từ {{ $productMessage->sender->name }} đến {{ $productMessage->receiver->name }}
        </div>
        <div class="card-body">
            <p><strong>Sản phẩm:</strong> {{ $productMessage->product->name }}</p>
            <p><strong>Trạng thái:</strong> 
                <span class="badge bg-{{ $productMessage->status == 'read' ? 'success' : 'warning' }}">
                    {{ ucfirst($productMessage->status) }}
                </span>
            </p>
            <p><strong>Nội dung tin nhắn:</strong></p>
            <p>{{ $productMessage->content }}</p>
        </div>
    </div>

    <a href="{{ route('admin.messages.index') }}" class="btn btn-primary mt-3">Quay lại danh sách tin nhắn</a>
</div>
@endsection
