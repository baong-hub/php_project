<?php

namespace Tests\Feature;

use App\Models\LopHoc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Các trang quản lý yêu cầu đăng nhập
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_trang_chu_hien_thi_dashboard(): void
    {
        LopHoc::create([
            'ten_lop' => 'Lớp Dashboard',
            'ma_lop' => 'DB-001',
            'giao_vien' => 'Giáo viên A',
            'si_so' => 30,
            'trang_thai' => true,
        ]);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Lớp đông sinh viên nhất');
        $response->assertSee('Lớp Dashboard');
    }
}
