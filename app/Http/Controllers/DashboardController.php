<?php

namespace App\Http\Controllers;

use App\Models\LopHoc;
use App\Models\Menu;
use App\Models\SinhVien;

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
                'menu'            => Menu::count(),
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
