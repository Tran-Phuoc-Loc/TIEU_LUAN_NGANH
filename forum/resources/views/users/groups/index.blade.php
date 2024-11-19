@extends('layouts.users')

@section('title', 'Các Nhóm Tôi Tham Gia')

@section('content')
<style>
    .group-container {
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    .group-item {
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }

    .group-link {
        font-size: 1.2rem;
        color: #007bff;
        text-decoration: none;
    }

    .group-link:hover {
        text-decoration: underline;
    }

    .delete-button {
        color: #ff4d4d;
        background: none;
        border: none;
        font-weight: bold;
        cursor: pointer;
    }

    .request-list {
        margin-top: 10px;
        background-color: #f1f1f1;
        padding: 10px;
        border-radius: 5px;
    }

    .empty-group-message {
        text-align: center;
        padding: 30px;
        color: #777;
        font-size: 1.1rem;
    }
</style>

@include('layouts.partials.sidebar')
<div class="col-lg-6 col-md-7 offset-lg-2 content-col" style="border: 2px solid #e1e1e2; background-color:#fff; margin-left: 17%; overflow-y: auto;">
    <h2 class="text-center">Các Nhóm Tôi Tham Gia</h2>
    @if($groups->isNotEmpty())
    <ul class="list-group">
        @foreach ($groups as $group)
        <li class="group-item d-flex justify-content-between align-items-center">
            <a href="{{ route('users.index', ['group_id' => $group->id]) }}" class="group-link">
                <strong>{{ $group->name }}</strong>
            </a>
        </li>
        @endforeach
    </ul>
    @else
    <p>Bạn chưa tham gia nhóm nào.</p>
    @endif
</div>

<div class="col-lg-3 col-md-3 mt-lg-0 right-sidebar" style="background-color: #fff; width: 32%; margin-left: auto;">
    <h2 class="text-center">Các Nhóm Gợi Ý</h2>
    @if($suggestedGroups->isNotEmpty())
    <ul class="list-group">
        @foreach ($suggestedGroups as $suggestedGroup)
        <li class="group-item d-flex justify-content-between align-items-center">
            <a href="{{ route('users.index', ['group_id' => $suggestedGroup->id]) }}" class="group-link">
                <strong>{{ $suggestedGroup->name }}</strong>
            </a>
            <form action="{{ route('groups.join', $suggestedGroup->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">Tham Gia</button>
            </form>
        </li>
        @endforeach
    </ul>
    @else
    <p>Không có nhóm gợi ý nào.</p>
    @endif
</div>
@endsection