<?php

namespace Database\Seeders;

use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Không dùng WithoutModelEvents: LopHoc cần event "saving" để điền tên giảng viên

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
            $giangVienIds = User::where('role', User::ROLE_GIANG_VIEN)->pluck('id');

            LopHoc::factory()
                ->count(5)
                ->state(fn () => ['giang_vien_id' => $giangVienIds->random()])
                ->has(SinhVien::factory()->count(4))
                ->create();
        }
    }
}
