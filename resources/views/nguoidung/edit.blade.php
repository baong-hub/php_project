{{-- resources/views/nguoidung/edit.blade.php --}}
@extends('layouts.layoutmaster')

@section('content')
    <div class="page-title">Sửa người dùng</div>
    <div class="page-sub">Cập nhật thông tin người dùng: «{{ $user->name }}»</div>

    <div class="form-box">
        <form action="{{ route('nguoidung.update', $user->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            @include('nguoidung._form', ['user' => $user])

            <div class="form-actions">
                <button type="submit" class="btn-submit">Cập nhật</button>
                <a href="{{ route('nguoidung.index') }}" class="btn-cancel">Hủy</a>
            </div>
        </form>
    </div>
@endsection
