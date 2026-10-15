<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Tài khoản mẫu, mật khẩu đều là "password".
     */
    public function run(): void
    {
        // Tài khoản cố định để đăng nhập thử (chạy lại seeder không bị trùng)
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Quản trị viên', 'role' => User::ROLE_ADMIN, 'trang_thai' => true, 'password' => 'password']
        );

        User::updateOrCreate(
            ['email' => 'giangvien@example.com'],
            ['name' => 'Nguyễn Văn Giảng', 'role' => User::ROLE_GIANG_VIEN, 'trang_thai' => true, 'password' => 'password']
        );

        // Thêm giảng viên ngẫu nhiên bằng factory (chỉ tạo khi còn ít, để chạy lại không sinh thêm mãi)
        $soCanTao = 8 - User::count();
        if ($soCanTao > 0) {
            User::factory()->giangVien()->count($soCanTao)->create();
        }
    }
}
