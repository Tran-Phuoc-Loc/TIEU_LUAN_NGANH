@extends('layouts.app')

@section('content')
    <h1>Edit Post</h1>
    <form action="{{ route('posts.update', $post) }}" method="POST">
        @csrf
        @method('PUT')
        
        <label for="title">Title</label>
        <input type="text" name="title" id="title" value="{{ $post->title }}" required>
        
        <label for="body">Body</label>
        <textarea name="body" id="body" required>{{ $post->body }}</textarea>
        
        <label for="categories">Categories</label>
        <select name="categories[]" id="categories" multiple>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ $post->categories->contains($category) ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        
        <button type="submit">Update</button>
    </form>
@endsection

