@extends('layouts.users')

@section('title', 'Chỉnh sửa bài viết')

@section('content')
@include('layouts.partials.sidebar')

        <!-- Nội dung bài viết chính -->
        <div class="col-lg-6 col-md-7 offset-lg-2 content-col" style="border: 2px solid #007bff; background-color:#fff; margin-left: 17%;">
            <h2>Chỉnh sửa bài viết</h2>

            <form action="{{ route('forums.update', $post->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
                </div>

                <!-- Thêm trường chọn danh mục -->
                <div class="form-group">
                    <label for="forum_category_id">Danh mục</label>
                    <select name="forum_category_id" class="form-control" required>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $category->id == $post->forum_category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="content">Nội dung</label>
                    <textarea id="content" name="content" class="form-control" rows="5" required>{!! old('content', $post->content) !!}</textarea>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Cập nhật</button>
                <a href="{{ route('forums.show', $post->id) }}" class="btn btn-secondary mt-3" style="padding: 15px; font-size:16px;">Hủy</a>
            </form>
        </div>

        <!-- Sidebar danh mục diễn đàn bên phải -->
        <div class="col-lg-3 col-md-3 mt-lg-0 right-sidebar" style="background-color: #fff; width: 32%; margin-left: auto;">
            <h1>Diễn Đàn</h1>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Danh mục -->
            <h2>Danh Mục</h2>
            <ul>
                @foreach($categories as $category)
                <li>
                    <strong>{{ $category->name }}</strong>
                    @if($category->posts->isNotEmpty())
                    <ul>
                        @foreach($category->posts as $post)
                        <li>
                            <a href="{{ route('forums.show', $post->id) }}">{{ $post->title }}</a> -
                            <em>{{ $post->user->username ?? 'Không có tên' }}</em>
                            ({{ $post->created_at->diffForHumans() }})
                            <p>Thời gian cập nhật: {{ $post->updated_at->format('d/m/Y H:i') }}</p>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </li>
                @endforeach
            </ul>

            <!-- Bài viết mới nhất -->
            <h2>Bài Viết Mới Nhất</h2>
            <ul>
                @foreach($latestPosts as $post)
                <li>
                    <a href="{{ route('forums.show', $post->id) }}">{{ $post->title }}</a> -
                    <em>{{ $post->user->username ?? 'Không có tên' }}</em>
                    ({{ $post->created_at->diffForHumans() }})
                </li>
                @endforeach
            </ul>

        </div>
    </div>
</div>
@endsection