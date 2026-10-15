{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.layoutmaster')

@push('styles')
<style>
    .login-wrap{ min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px 0; }
    .login-card{ width:100%; max-width:400px; background:#fff; border:1px solid var(--line); border-radius:var(--radius); padding:32px 30px; box-shadow:0 8px 30px rgb(22 33 62 / 8%); }
    .login-brand{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
    .login-brand .mark{
        width:40px; height:40px; border-radius:50%; background:var(--gold); color:var(--navy);
        display:flex; align-items:center; justify-content:center; font-family:'Fraunces', serif; font-weight:700;
    }
    .login-brand .title{ font-family:'Fraunces', serif; font-size:19px; font-weight:700; color:var(--navy); }
    .login-brand .sub{ font-size:11px; color:var(--ink-muted); letter-spacing:.06em; text-transform:uppercase; }
    .form-group{ margin-bottom:16px; }
    .form-group label{ display:block; font-size:13px; font-weight:600; margin-bottom:6px; }
    .form-group input{
        width:100%; padding:10px 12px; border:1px solid var(--line); border-radius:8px;
        font-size:13.5px; font-family:'Inter', sans-serif; outline:none; transition:border-color .15s ease;
    }
    .form-group input:focus{ border-color:var(--gold); }
    .field-error{ color:#a32626; font-size:12px; margin-top:5px; }
    .form-check{ display:flex; align-items:center; gap:8px; margin-bottom:20px; font-size:13px; }
    .btn-submit{ width:100%; background:var(--navy); color:#F5F1E8; border:none; padding:11px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:background .15s ease; }
    .btn-submit:hover{ background:var(--navy-2); }
    .demo-accounts{ margin-top:20px; padding-top:16px; border-top:1px dashed var(--line); font-size:12px; color:var(--ink-muted); line-height:1.7; }
    .demo-accounts code{ color:var(--navy); }
</style>
@endpush

@section('content')
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-brand">
                <div class="mark">QL</div>
                <div>
                    <div class="title">Quản lý Sinh viên</div>
                    <div class="sub">Đăng nhập hệ thống</div>
                </div>
            </div>

            <form action="{{ route('login.store') }}" method="POST" novalidate>
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus autocomplete="username">
                    @error('email') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" autocomplete="current-password">
                    @error('password') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-check">
                    <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit" class="btn-submit">Đăng nhập</button>
            </form>

            @if (app()->environment('local'))
                <div class="demo-accounts">
                    Tài khoản mẫu (sau khi chạy seeder), mật khẩu <code>password</code>:<br>
                    Admin: <code>admin@example.com</code><br>
                    Giảng viên: <code>giangvien@example.com</code>
                </div>
            @endif
        </div>
    </div>
@endsection
