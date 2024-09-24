@extends('layouts.users')

@section('content')
<div class="welcome-contents">
    <h1>Bài Viết Draft</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($drafts->isEmpty())
        <p>Không có bài viết nào trong trạng thái draft.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Tiêu Đề</th>
                    <th>Nội Dung</th>
                    <th>Ngày Tạo</th>
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
    @endif
</div>
@endsection