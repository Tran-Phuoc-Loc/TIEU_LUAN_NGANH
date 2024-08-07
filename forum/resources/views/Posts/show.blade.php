@extends('layouts.app')

@section('content')
    <h1>{{ $post->title }}</h1>
    <p>{{ $post->body }}</p>
    <p>By: {{ $post->user->name }}</p>

    <h2>Comments</h2>
    <ul>
        @foreach ($post->comments as $comment)
            <li>{{ $comment->body }} - {{ $comment->user->name }}</li>
        @endforeach
    </ul>

    @auth
        <form action="{{ route('comments.store', $post) }}" method="POST">
            @csrf
            <textarea name="body" required></textarea>
            <button type="submit">Add Comment</button>
        </form>
    @endauth
@endsection

