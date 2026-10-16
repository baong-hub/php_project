<?php

namespace App\Http\Controllers;

use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Giảng viên chỉ thấy số liệu của các lớp mình chủ nhiệm
        $user = $request->user();
        $sinhVien = fn () => SinhVien::cuaNguoiDung($user);
        $lopHoc = fn () => LopHoc::cuaNguoiDung($user);

        return view('dashboard', [
            'title' => 'Dashboard',
            'thongKe' => [
                'sinhVien'        => $sinhVien()->count(),
                'sinhVienDangHoc' => $sinhVien()->where('trang_thai', true)->count(),
                'lopHoc'          => $lopHoc()->count(),
                'lopDangHoatDong' => $lopHoc()->where('trang_thai', true)->count(),
                'nguoiDung'       => User::count(),
                'giangVien'       => User::where('role', User::ROLE_GIANG_VIEN)->count(),
            ],
            // 5 sinh viên mới thêm gần nhất
            'sinhVienMoi' => $sinhVien()->with('lopHoc')->latest('id')->take(5)->get(),
            // 5 lớp có nhiều sinh viên nhất
            'lopDongNhat' => $lopHoc()->withCount('sinhViens')
                ->orderByDesc('sinh_viens_count')
                ->take(5)
                ->get(),
        ]);
    }
}
