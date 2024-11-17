@extends('layouts.users')

@section('title', 'Danh sách Bài Viết Đã Lưu')

@section('content')
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
            <h1 class="text-primary">Bài Viết Đã Lưu Theo Thư Mục</h1>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($folders->isEmpty())
            <p class="text-warning">Không có thư mục nào hoặc không có bài viết nào đã lưu.</p>
            @else
            @foreach($folders as $folder)
            <div class="col-md-12" style="margin-bottom: 12px;">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Thư mục: {{ $folder->name }}</h5>

                        <!-- Nút Xóa và Đổi Tên -->
                        <div class="folder-actions">
                            <form action="{{ route('folders.delete', $folder->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Bạn có chắc muốn xóa thư mục này không? Toàn bộ bài viết đã lưu sẽ bị xóa!')">
                                    Xóa
                                </button>
                            </form>

                            <button class="btn btn-secondary btn-sm rename-folder" data-folder-id="{{ $folder->id }}">
                                Đổi tên
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($folder->savedPosts->isEmpty())
                        <p class="text-muted">Thư mục này chưa có bài viết nào.</p>
                        @else
                        <ul class="list-group list-group-flush">
                            @foreach($folder->savedPosts as $savedPost)
                            <li class="list-group-item">
                                <a href="{{ route('users.index', ['post_id' => $savedPost->post->id]) }}" class="text-primary">
                                    {{ $savedPost->post->title }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            @endif
        </div>
    </div>

    <!-- Modal Đổi Tên -->
    <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="renameModalLabel">Đổi Tên Thư Mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="renameFolderForm" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="text" class="form-control" name="new_name" placeholder="Nhập tên mới cho thư mục" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @endsection