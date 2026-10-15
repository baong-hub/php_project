<?php

namespace App\Http\Controllers;

use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'title' => 'Dashboard',
            'thongKe' => [
                'sinhVien'        => SinhVien::count(),
                'sinhVienDangHoc' => SinhVien::where('trang_thai', true)->count(),
                'lopHoc'          => LopHoc::count(),
                'lopDangHoatDong' => LopHoc::where('trang_thai', true)->count(),
                'nguoiDung'       => User::count(),
                'giangVien'       => User::where('role', User::ROLE_GIANG_VIEN)->count(),
            ],
            // 5 sinh viên mới thêm gần nhất
            'sinhVienMoi' => SinhVien::with('lopHoc')->latest('id')->take(5)->get(),
            // 5 lớp có nhiều sinh viên nhất
            'lopDongNhat' => LopHoc::withCount('sinhViens')
                ->orderByDesc('sinh_viens_count')
                ->take(5)
                ->get(),
        ]);
    }
}
