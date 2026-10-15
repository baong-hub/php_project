<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    public function definition(): array
    {
        $ten = fake()->unique()->words(2, true);

        return [
            'ten'        => ucfirst($ten),
            'icon'       => fake()->randomElement(array_keys(Menu::ICONS)),
            'url'        => '/' . str_replace(' ', '-', $ten),
            'vi_tri'     => fake()->randomElement(array_keys(Menu::VI_TRI)),
            'nhom'       => fake()->randomElement(['Tổng quan', 'Học vụ', 'Hệ thống']),
            'thu_tu'     => fake()->numberBetween(1, 200),
            'trang_thai' => true,
            'chi_admin'  => false,
        ];
    }

    public function chiAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'chi_admin' => true,
        ]);
    }
}
