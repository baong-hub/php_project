{{-- resources/views/nguoidung/create.blade.php --}}
@extends('layouts.layoutmaster')

@section('content')
    <div class="page-title">Thêm người dùng</div>
    <div class="page-sub">Tạo tài khoản quản trị viên hoặc giảng viên.</div>

    <div class="form-box">
        <form action="{{ route('nguoidung.store') }}" method="POST" novalidate>
            @csrf

            @include('nguoidung._form', ['user' => null])

            <div class="form-actions">
                <button type="submit" class="btn-submit">Lưu người dùng</button>
                <a href="{{ route('nguoidung.index') }}" class="btn-cancel">Hủy</a>
            </div>
        </form>
    </div>
@endsection
