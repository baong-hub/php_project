<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Các menu sidebar mặc định (trước đây được viết cứng trong partials/sidebar).
     * Thứ tự cách nhau 10 để dễ chèn menu mới vào giữa.
     * chi_admin = true: giảng viên không nhìn thấy menu này.
     */
    public function run(): void
    {
        $menus = [
            ['ten' => 'Dashboard', 'icon' => 'dashboard', 'url' => '/', 'nhom' => 'Tổng quan', 'thu_tu' => 10, 'chi_admin' => false],
            ['ten' => 'Sinh viên', 'icon' => 'user', 'url' => '/sinhvien', 'nhom' => 'Học vụ', 'thu_tu' => 20, 'chi_admin' => false],
            ['ten' => 'Lớp học', 'icon' => 'book', 'url' => '/lophoc', 'nhom' => 'Học vụ', 'thu_tu' => 30, 'chi_admin' => false],
            ['ten' => 'Điểm số', 'icon' => 'check', 'url' => '/diem', 'nhom' => 'Học vụ', 'thu_tu' => 40, 'chi_admin' => false],
            ['ten' => 'Menu', 'icon' => 'menu', 'url' => '/menu', 'nhom' => 'Hệ thống', 'thu_tu' => 100, 'chi_admin' => true],
            ['ten' => 'Người dùng', 'icon' => 'users', 'url' => '/nguoidung', 'nhom' => 'Hệ thống', 'thu_tu' => 105, 'chi_admin' => true],
            ['ten' => 'Cài đặt', 'icon' => 'settings', 'url' => '/caidat', 'nhom' => 'Hệ thống', 'thu_tu' => 110, 'chi_admin' => false],
            ['ten' => 'Tài khoản của tôi', 'icon' => 'key', 'url' => '/taikhoan', 'nhom' => 'Hệ thống', 'thu_tu' => 120, 'chi_admin' => false],
        ];

        // Dùng url + vị trí làm khóa để chạy seeder nhiều lần không bị trùng.
        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['url' => $menu['url'], 'vi_tri' => 'sidebar'],
                $menu + ['trang_thai' => true]
            );
        }
    }
}
