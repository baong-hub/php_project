<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LopHoc;
use App\Models\User;

class LopHocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lớp phải có giảng viên chủ nhiệm -> đảm bảo đã có tài khoản giảng viên
        if (!User::where('role', User::ROLE_GIANG_VIEN)->exists()) {
            $this->call(UserSeeder::class);
        }

        // Tài khoản giangvien@example.com đứng đầu để luôn có lớp khi đăng nhập thử
        $giangVienIds = User::where('role', User::ROLE_GIANG_VIEN)
            ->orderByRaw("email = 'giangvien@example.com' desc")
            ->orderBy('id')
            ->pluck('id')
            ->all();

        // Tạo 20 lớp mẫu, chia đều cho các giảng viên.
        // Dùng mã lớp cố định để có thể chạy seeder nhiều lần mà không tạo trùng.
        for ($i = 1; $i <= 20; $i++) {
            $maLop = sprintf('DEMO-LH-%03d', $i);

            LopHoc::updateOrCreate(
                ['ma_lop' => $maLop],
                [
                    'ten_lop' => sprintf('Lớp mẫu %02d', $i),
                    'giang_vien_id' => $giangVienIds[($i - 1) % count($giangVienIds)],
                    'ghi_chu' => 'Dữ liệu mẫu để thực hành phân trang.',
                    'si_so' => 20 + (($i * 7) % 31),
                    'trang_thai' => true,
                ]
            );
        }
    }
}
