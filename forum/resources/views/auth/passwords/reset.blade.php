@extends('layouts.admin')

@section('content')
    <h2>Đặt lại mật khẩu</h2>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
            <label for="email">Địa chỉ email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu mới</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password-confirm">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" id="password-confirm" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Đặt lại mật khẩu</button>
    </form>
@endsection