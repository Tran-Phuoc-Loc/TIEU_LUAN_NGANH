@extends('layouts.users')

@section('title', 'Danh Sách Danh Mục')

@section('content')
@include('layouts.partials.sidebar')
<div class="col-lg-10 col-md-10 offset-lg-2 content-col" style="border: 2px solid #c8ccd0; background-color:#fff;">
    <div class="post-container mb-4">
        <div class="row">
            <h1 class="text-center">Danh Sách Danh Mục</h1>

            @if ($categories->isEmpty())
            <div class="empty-message">
                <p>Không có danh mục nào.</p>
            </div>
            @else
            <ul class="list-group">
                @foreach ($categories as $category)
                <li class="list-group-item category-item">
                    <a href="{{ route('categories.posts', ['slug' => $category->slug]) }}" class="category-link">
                        {{ $category->name }}
                    </a>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection