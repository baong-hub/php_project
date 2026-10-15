<?php

namespace Database\Seeders;

use App\Models\LopHoc;
use App\Models\SinhVien;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Gọi tới các Seeder con
        $this->call([
            UserSeeder::class,
            SinhVienSeeder::class, // tự gọi LopHocSeeder trước
            MenuSeeder::class,
        ]);

        // Sinh thêm dữ liệu ngẫu nhiên bằng factory: 5 lớp, mỗi lớp 4 sinh viên.
        // Chỉ chạy lần đầu để chạy lại seeder không bị nhân đôi dữ liệu.
        if (LopHoc::count() <= 20) {
            LopHoc::factory()
                ->count(5)
                ->has(SinhVien::factory()->count(4))
                ->create();
        }
    }
}
