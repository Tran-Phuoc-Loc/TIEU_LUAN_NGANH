@extends('layouts.users')

@section('content')
<div class="row">
    <div class="post-container">
    <h1>Danh Sách Danh Mục</h1>

    @if ($categories->isEmpty())
        <p>Không có danh mục nào.</p>
    @else
        <ul>
            @foreach ($categories as $category)
                <li>{{ $category->name }}</li>
            @endforeach
        </ul>
    @endif
    </div>
</div>
@endsection
