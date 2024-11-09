@extends('layouts.users')

@section('content')
<style>
    .table th {
        background-color: #000;
    }
</style>
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
            <h1 class="mb-4">Bài Viết Draft</h1>

            <!-- Hiển thị thông báo thành công nếu có -->
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <!-- Kiểm tra nếu không có bài viết -->
            @if($drafts->isEmpty())
            <p>Không có bài viết nào trong trạng thái draft.</p>
            @else
            <!-- Thêm table-responsive để bảng có thể cuộn ngang trên thiết bị di động -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Tiêu Đề</th>
                            <th>Nội Dung</th>
                            <th>Ngày Tạo</th>
                            <th>Ảnh</th>
                            <th>Tùy Chọn</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($drafts as $draft)
                        <tr>
                            <td>{{ $draft->title }}</td>
                            <td>{{ Str::limit($draft->content, 50) }}</td>
                            <td>{{ $draft->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($draft->image_url)
                                <img src="{{ asset('storage/' . $draft->image_url) }}" alt="{{ $draft->title }}" class="img-fluid rounded" style="width: 100px; height: auto;" loading="lazy">
                                @else
                                <span class="text-muted">Không có ảnh</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('posts.edit', $draft->id) }}" class="btn btn-primary btn-sm">Chỉnh Sửa</a>
                                <form action="{{ route('posts.destroy', $draft->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endsection