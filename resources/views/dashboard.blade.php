{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.layoutmaster')

@push('styles')
<style>
    .stat-grid{ display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:16px; margin-bottom:26px; }
    .stat-card{
        display:block; background:#fff; border:1px solid var(--line); border-radius:var(--radius);
        padding:18px 20px; text-decoration:none; color:var(--ink); transition:border-color .15s ease;
    }
    .stat-card:hover{ border-color:var(--gold); color:var(--ink); }
    .stat-label{ font-size:12px; color:var(--ink-muted); font-weight:600; text-transform:uppercase; letter-spacing:.06em; }
    .stat-value{ font-family:'Fraunces', serif; font-size:30px; font-weight:700; color:var(--navy); margin-top:6px; }
    .stat-note{ font-size:12px; color:var(--ink-muted); margin-top:2px; }

    .panel-grid{ display:grid; grid-template-columns:3fr 2fr; gap:16px; }
    .panel{ background:#fff; border:1px solid var(--line); border-radius:var(--radius); overflow:hidden; }
    .panel-head{ display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border-bottom:1px solid var(--line); }
    .panel-head h2{ font-size:16px; margin:0; color:var(--navy); }
    .panel-head a{ font-size:12.5px; color:var(--navy); font-weight:600; text-decoration:none; }
    .panel-head a:hover{ text-decoration:underline; }
    .panel table{ width:100%; border-collapse:collapse; font-size:13.5px; }
    .panel td{ padding:11px 18px; border-bottom:1px solid #edf0f3; vertical-align:middle; }
    .panel tr:last-child td{ border-bottom:none; }
    .panel td a{ color:var(--navy); font-weight:600; text-decoration:none; }
    .panel td a:hover{ text-decoration:underline; }
    .muted{ color:var(--ink-muted); font-size:12.5px; }
    .text-end{ text-align:right; }
    .empty{ padding:24px 18px; color:var(--ink-muted); font-size:13px; text-align:center; }

    @media (max-width: 1100px){
        .stat-grid{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
        .panel-grid{ grid-template-columns:1fr; }
    }
</style>
@endpush

@section('content')
    <div class="page-title">Dashboard</div>
    <div class="page-sub">Tổng quan hệ thống quản lý sinh viên.</div>

    <div class="stat-grid">
        <a href="{{ route('sinhvien.index') }}" class="stat-card">
            <div class="stat-label">Sinh viên</div>
            <div class="stat-value">{{ number_format($thongKe['sinhVien']) }}</div>
            <div class="stat-note">{{ number_format($thongKe['sinhVienDangHoc']) }} đang hoạt động</div>
        </a>
        <a href="{{ route('lophoc.index') }}" class="stat-card">
            <div class="stat-label">Lớp học</div>
            <div class="stat-value">{{ number_format($thongKe['lopHoc']) }}</div>
            <div class="stat-note">Tổng số lớp</div>
        </a>
        <a href="{{ route('sinhvien.index') }}" class="stat-card">
            <div class="stat-label">TB sinh viên / lớp</div>
            <div class="stat-value">{{ $thongKe['lopHoc'] ? number_format($thongKe['sinhVien'] / $thongKe['lopHoc'], 1) : 0 }}</div>
            <div class="stat-note">Tính trên tất cả lớp</div>
        </a>
        <a href="{{ route('menu.index') }}" class="stat-card">
            <div class="stat-label">Menu</div>
            <div class="stat-value">{{ number_format($thongKe['menu']) }}</div>
            <div class="stat-note">Mục menu trong hệ thống</div>
        </a>
    </div>

    <div class="panel-grid">
        <div class="panel">
            <div class="panel-head">
                <h2>Sinh viên mới thêm</h2>
                <a href="{{ route('sinhvien.index') }}">Xem tất cả →</a>
            </div>
            @if ($sinhVienMoi->isEmpty())
                <div class="empty">Chưa có sinh viên nào.</div>
            @else
                <table>
                    @foreach ($sinhVienMoi as $sv)
                        <tr>
                            <td class="mono muted">{{ $sv->ma_sv }}</td>
                            <td><a href="{{ route('sinhvien.show', $sv->id) }}">{{ $sv->ho_ten }}</a></td>
                            <td class="muted">{{ $sv->lopHoc->ten_lop ?? '—' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Lớp đông sinh viên nhất</h2>
                <a href="{{ route('lophoc.index') }}">Xem tất cả →</a>
            </div>
            @if ($lopDongNhat->isEmpty())
                <div class="empty">Chưa có lớp học nào.</div>
            @else
                <table>
                    @foreach ($lopDongNhat as $lop)
                        <tr>
                            <td><a href="{{ route('lophoc.show', $lop->id) }}">{{ $lop->ten_lop }}</a></td>
                            <td class="text-end muted">{{ $lop->sinh_viens_count }} sinh viên</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
@endsection
