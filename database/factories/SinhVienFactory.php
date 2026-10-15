<?php

namespace Database\Factories;

use App\Models\LopHoc;
use Illuminate\Database\Eloquent\Factories\Factory;

class SinhVienFactory extends Factory
{
    public function definition(): array
    {
        $ho = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Vũ', 'Đặng', 'Bùi', 'Đỗ', 'Phan'];
        $dem = ['Văn', 'Thị', 'Minh', 'Ngọc', 'Đức', 'Thu', 'Quang', 'Thanh'];
        $ten = ['An', 'Bình', 'Chi', 'Dũng', 'Giang', 'Hà', 'Khánh', 'Linh', 'Minh', 'Nam', 'Phương', 'Trang'];

        return [
            'ma_sv'         => strtoupper(fake()->unique()->bothify('SV######')),
            'ho_ten'        => fake()->randomElement($ho) . ' ' . fake()->randomElement($dem) . ' ' . fake()->randomElement($ten),
            'email'         => fake()->unique()->safeEmail(),
            'ngay_sinh'     => fake()->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
            'gioi_tinh'     => fake()->boolean(),
            'lop_hoc_id'    => LopHoc::factory(),
            'so_dien_thoai' => '09' . fake()->numerify('########'),
            'dia_chi'       => fake()->optional()->address(),
            'trang_thai'    => fake()->boolean(90),
        ];
    }

    /**
     * Sinh viên chưa được xếp lớp.
     */
    public function chuaXepLop(): static
    {
        return $this->state(fn (array $attributes) => [
            'lop_hoc_id' => null,
        ]);
    }
}
