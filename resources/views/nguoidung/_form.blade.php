{{-- Form dùng chung cho thêm / sửa người dùng. Biến: $user (null khi thêm mới), $roleOptions --}}
@push('styles')
<style>
    .form-box{ max-width: 520px; background: var(--paper-2); border: 1px solid var(--line); border-radius: var(--radius); padding: 28px; }
    .form-group{ margin-bottom: 18px; }
    .form-group label{ display:block; font-size:13px; font-weight:600; margin-bottom:6px; color: var(--ink); }
    .form-group input, .form-group select{
        width:100%; padding:10px 12px; border:1px solid var(--line); border-radius:8px; background:#fff;
        font-size:13.5px; font-family:'Inter', sans-serif; outline:none; transition:border-color .15s ease;
    }
    .form-group input:focus, .form-group select:focus{ border-color:var(--gold); }
    .form-hint{ font-size:11.5px; color:var(--ink-muted); margin-top:4px; }
    .field-error{ color:#a32626; font-size:12px; margin-top:5px; }
    .form-check{ display:flex; align-items:center; gap:8px; margin-bottom:6px; }
    .form-check input{ width:auto; }
    .form-check label{ margin:0; font-weight:500; font-size:13.5px; }
    .form-actions{ display:flex; gap:10px; margin-top:22px; }
    .btn-submit{ background: var(--navy); color:#F5F1E8; border:none; padding:10px 22px; border-radius:8px; font-size:13.5px; font-weight:600; cursor:pointer; }
    .btn-submit:hover{ background: var(--navy-2); }
    .btn-cancel{ display:inline-flex; align-items:center; padding:10px 22px; border-radius:8px; font-size:13.5px; font-weight:600; color:var(--ink); background:#fff; border:1px solid var(--line); text-decoration:none; }
    .btn-cancel:hover{ border-color:var(--gold); color:var(--ink); }
</style>
@endpush

<div class="form-group">
    <label for="name">Họ tên</label>
    <input type="text" id="name" name="name" value="{{ old('name', $user?->name) }}" placeholder="Ví dụ: Nguyễn Văn An">
    @error('name') <div class="field-error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email', $user?->email) }}" placeholder="ten@example.com">
    @error('email') <div class="field-error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="role">Vai trò</label>
    <select id="role" name="role">
        @foreach ($roleOptions as $key => $label)
            <option value="{{ $key }}" {{ old('role', $user?->role ?? 'giang_vien') === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <p class="form-hint">Giảng viên chỉ quản lý được lớp học, sinh viên và tài khoản của bản thân.</p>
    @error('role') <div class="field-error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="password">Mật khẩu</label>
    <input type="password" id="password" name="password" autocomplete="new-password">
    <p class="form-hint">Tối thiểu 8 ký tự.@if ($user) Để trống nếu không muốn đổi mật khẩu.@endif</p>
    @error('password') <div class="field-error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="password_confirmation">Xác nhận mật khẩu</label>
    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
</div>

<div class="form-check">
    <input type="checkbox" id="trang_thai" name="trang_thai" value="1"
           {{ old('trang_thai', $user?->trang_thai ?? true) ? 'checked' : '' }}>
    <label for="trang_thai">Đang hoạt động (bỏ chọn để khóa tài khoản)</label>
</div>
@error('trang_thai') <div class="field-error">{{ $message }}</div> @enderror
