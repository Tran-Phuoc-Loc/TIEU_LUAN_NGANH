@extends('layouts.users')

@section('title', 'Bài viết trong danh mục')

@section('content')
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
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