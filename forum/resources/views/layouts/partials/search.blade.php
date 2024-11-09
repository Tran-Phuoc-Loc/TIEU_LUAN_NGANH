<form method="GET" action="{{ $action }}" class="mb-3">
    <div class="input-group">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $placeholder }}" class="form-control" />
        <button type="submit" class="btn btn-secondary">Tìm kiếm</button>
    </div>
</form>
