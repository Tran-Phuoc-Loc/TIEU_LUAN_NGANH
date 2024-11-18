@extends('layouts.users')

@section('title', 'Danh sách Bài Viết Xuất Bản')

@section('content')
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
            <h1>Bài Viết Đã Xuất Bản</h1>

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if($published->isEmpty())
            <p>{{ $isCurrentUser ? 'Bạn chưa có bài viết nào đã xuất bản.' : 'Hiện tại người dùng này chưa có bài viết nào đã xuất bản.' }}</p>
            @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tiêu Đề</th>
                            <th>Nội Dung</th>
                            <th>Ngày Xuất Bản</th>
                            <th>Danh Mục</th>
                            <th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($published as $post)
                        <tr>
                            <td><a href="{{ route('users.index', ['post_id' => $post->id]) }}">{{ $post->title }}</a></td>
                            <td>{!! Str::limit($post->content, 50) !!}</td>
                            <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $post->category->name ?? 'Không có danh mục' }}</td>
                            <td>{{ ucfirst($post->status) }}</td> <!-- Hiển thị trạng thái với chữ cái đầu in hoa -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endsection