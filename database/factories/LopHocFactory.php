<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LopHocFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ten_lop'       => 'Lớp ' . fake()->word(),
            'ma_lop'        => strtoupper(fake()->unique()->bothify('LH###')),
            // giao_vien (tên hiển thị) được model tự điền theo giảng viên chủ nhiệm
            'giang_vien_id' => User::factory()->giangVien(),
            'ghi_chu'       => fake()->optional()->sentence(),
            'si_so'         => fake()->numberBetween(20, 100),
            'trang_thai'    => fake()->boolean(),
        ];
    }

    /**
     * Lớp do một giảng viên có sẵn chủ nhiệm.
     */
    public function cuaGiangVien(User $giangVien): static
    {
        return $this->state(fn (array $attributes) => [
            'giang_vien_id' => $giangVien->id,
        ]);
    }
}
