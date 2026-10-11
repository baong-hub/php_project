<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Các menu sidebar mặc định (trước đây được viết cứng trong partials/sidebar).
     * Thứ tự cách nhau 10 để dễ chèn menu mới vào giữa.
     */
    public function run(): void
    {
        $menus = [
            ['ten' => 'Dashboard', 'icon' => 'dashboard', 'url' => '/', 'nhom' => 'Tổng quan', 'thu_tu' => 10],
            ['ten' => 'Sinh viên', 'icon' => 'user', 'url' => '/sinhvien', 'nhom' => 'Học vụ', 'thu_tu' => 20],
            ['ten' => 'Lớp học', 'icon' => 'book', 'url' => '/lophoc', 'nhom' => 'Học vụ', 'thu_tu' => 30],
            ['ten' => 'Điểm số', 'icon' => 'check', 'url' => '/diem', 'nhom' => 'Học vụ', 'thu_tu' => 40],
            ['ten' => 'Menu', 'icon' => 'menu', 'url' => '/menu', 'nhom' => 'Hệ thống', 'thu_tu' => 100],
            ['ten' => 'Cài đặt', 'icon' => 'settings', 'url' => '/caidat', 'nhom' => 'Hệ thống', 'thu_tu' => 110],
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
