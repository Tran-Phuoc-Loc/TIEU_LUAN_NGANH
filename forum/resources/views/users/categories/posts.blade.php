@extends('layouts.users')

@section('content')
<div class="row">
    <div class="post-container">
        <h1>Bài viết trong danh mục: {{ $category->name }}</h1>
        
        <!-- Debug information -->
        <p>Category ID: {{ $category->id }}</p>
        <p>Number of posts: {{ $posts->count() }}</p>

        @if ($posts->isEmpty())
            <p>Không có bài viết nào trong danh mục này.</p>
        @else
            <ul>
                @foreach ($posts as $post)
                    <li>
                        <a href="{{ route('users.index', ['post' => $post->id]) }}">
                            {{ $post->title }}
                        </a> - {{ $post->created_at->format('d/m/Y') }}
                        <!-- Debug: Show post category ID -->
                        (Category ID: {{ $post->category_id }})
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection