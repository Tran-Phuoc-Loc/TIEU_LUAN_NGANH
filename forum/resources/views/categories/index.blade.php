@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <h1>All Categories</h1>
    <a href="{{ route('categories.create') }}">Create a new category</a>
    @foreach($categories as $category)
        <div>
            <h2>{{ $category->name }}</h2>
        </div>
    @endforeach
@endsection
