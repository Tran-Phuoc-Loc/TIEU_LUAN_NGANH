@extends('layouts.users')

@section('content')
<div class="container">
    <h1 class="my-4">{{ $category->name }}</h1>
    <p>{{ $category->description }}</p>

    @if($category->posts->isEmpty())
        <p>Hiện tại không có bài viết nào trong danh mục này.</p>
    @else
        <div class="list-group">
            @foreach($category->posts as $post)
                <a href="#" class="list-group-item list-group-item-action">
                    <h5 class="mb-1">{{ $post->title }}</h5>
                    <small>Bởi: {{ $post->user->name }} vào {{ $post->created_at->format('d/m/Y') }}</small>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
