@extends('layouts.users')

@section('title', 'Danh sách tìm kiếm')

@section('content')
@include('layouts.partials.sidebar')

<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
            <h2 class="mb-4">Kết quả tìm kiếm cho: "{{ request('query') }}"</h2>

            {{-- Chỉ hiển thị khi có ít nhất một kết quả --}}
            @if ($forumPosts->isNotEmpty() || $posts->isNotEmpty() || $products->isNotEmpty() || $users->isNotEmpty() || $groups->isNotEmpty())
            
                {{-- Kết quả tìm kiếm bài viết diễn đàn --}}
                @if ($forumPosts->isNotEmpty())
                <div class="mb-5">
                    <h3 class="mb-3">Bài Viết Diễn Đàn</h3>
                    <div class="row">
                        @foreach ($forumPosts as $post)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('forums.index', ['id' => $post->id]) }}">{{ $post->title }}</a>
                                    </h5>
                                    <p class="card-text">{{ Str::limit($post->content, 100) }}</p>
                                    <p class="text-muted">
                                        Viết bởi: {{ $post->user->username ?? 'Không có tên' }} <br>
                                        Ngày: {{ $post->created_at->format('d-m-Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Kết quả tìm kiếm bài viết mạng xã hội --}}
                @if ($posts->isNotEmpty())
                <div class="mb-5">
                    <h3 class="mb-3">Bài Viết Mạng Xã Hội</h3>
                    <div class="row">
                        @foreach ($posts as $post)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('users.index', ['id' => $post->id]) }}">{{ $post->title }}</a>
                                    </h5>
                                    <p class="card-text">{{ Str::limit($post->content, 100) }}</p>
                                    <p class="text-muted">
                                        Viết bởi: {{ $post->user->username ?? 'Không có tên' }} <br>
                                        Ngày: {{ $post->created_at->format('d-m-Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Kết quả tìm kiếm sản phẩm --}}
                @if ($products->isNotEmpty())
                <div class="mb-5">
                    <h3 class="mb-3">Sản phẩm</h3>
                    <div class="row">
                        @foreach ($products as $product)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('products.index', ['id' => $product->id]) }}">{{ $product->name }}</a>
                                    </h5>
                                    <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                                    <p class="text-muted">
                                        Đăng bởi: {{ $product->user->username ?? 'Không có tên' }} <br>
                                        Ngày: {{ $product->created_at->format('d-m-Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Kết quả tìm kiếm người dùng --}}
                @if ($users->isNotEmpty())
                <div class="mb-5">
                    <h3 class="mb-3">Người Dùng</h3>
                    <div class="row">
                        @foreach ($users as $user)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('users.profile.index', $user->id) }}">{{ $user->username }}</a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Kết quả tìm kiếm nhóm --}}
                @if ($groups->isNotEmpty())
                <div class="mb-5">
                    <h3 class="mb-3">Nhóm</h3>
                    <div class="row">
                        @foreach ($groups as $group)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('users.groups.show', $group->id) }}">{{ $group->name }}</a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            @else
                <p>Không tìm thấy kết quả nào phù hợp.</p>
            @endif
        </div>
    </div>
</div>
@endsection