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
                            <form action="{{ route('posts.publish', $draft->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Xuất Bản</button>
                            </form>
                            <form action="{{ route('posts.destroy', $draft->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection