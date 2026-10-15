{{-- resources/views/taikhoan/edit.blade.php --}}
@extends('layouts.layoutmaster')

@push('styles')
<style>
    .form-box{ max-width: 520px; background: var(--paper-2); border: 1px solid var(--line); border-radius: var(--radius); padding: 28px; }
    .form-section{ font-family:'Fraunces', serif; font-size:16px; color:var(--navy); margin:4px 0 14px; }
    .form-section + .form-section-sub{ font-size:12px; color:var(--ink-muted); margin:-10px 0 14px; }
    .form-divider{ border:none; border-top:1px solid var(--line); margin:22px 0; }
    .form-group{ margin-bottom: 18px; }
    .form-group label{ display:block; font-size:13px; font-weight:600; margin-bottom:6px; color: var(--ink); }
    .form-group input{
        width:100%; padding:10px 12px; border:1px solid var(--line); border-radius:8px; background:#fff;
        font-size:13.5px; font-family:'Inter', sans-serif; outline:none; transition:border-color .15s ease;
    }
    .form-group input:focus{ border-color:var(--gold); }
    .form-group input[readonly]{ background:var(--paper); color:var(--ink-muted); }
    .field-error{ color:#a32626; font-size:12px; margin-top:5px; }
    .btn-submit{ background: var(--navy); color:#F5F1E8; border:none; padding:10px 22px; border-radius:8px; font-size:13.5px; font-weight:600; cursor:pointer; }
    .btn-submit:hover{ background: var(--navy-2); }
</style>
@endpush

@section('content')
    <div class="page-title">Tài khoản của tôi</div>
    <div class="page-sub">Cập nhật thông tin cá nhân và mật khẩu đăng nhập.</div>

    <div class="form-box">
        <form action="{{ route('taikhoan.update') }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="form-section">Thông tin cá nhân</div>

            <div class="form-group">
                <label for="name">Họ tên</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                @error('name') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
                @error('email') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Vai trò</label>
                <input type="text" value="{{ $user->tenVaiTro() }}" readonly>
            </div>

            <hr class="form-divider">

            <div class="form-section">Đổi mật khẩu</div>
            <div class="form-section-sub">Để trống nếu không muốn đổi mật khẩu.</div>

            <div class="form-group">
                <label for="current_password">Mật khẩu hiện tại</label>
                <input type="password" id="current_password" name="current_password" autocomplete="current-password">
                @error('current_password') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu mới</label>
                <input type="password" id="password" name="password" autocomplete="new-password">
                @error('password') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
            </div>

            <button type="submit" class="btn-submit">Lưu thay đổi</button>
        </form>
    </div>
@endsection
