@extends('layouts.users')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="category-container">
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