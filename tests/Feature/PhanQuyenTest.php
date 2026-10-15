<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhanQuyenTest extends TestCase
{
    use RefreshDatabase;

    public function test_giang_vien_duoc_quan_ly_lop_hoc_sinh_vien_va_tai_khoan(): void
    {
        $this->actingAs(User::factory()->giangVien()->create());

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('lophoc.index'))->assertOk();
        $this->get(route('lophoc.create'))->assertOk();
        $this->get(route('sinhvien.index'))->assertOk();
        $this->get(route('sinhvien.create'))->assertOk();
        $this->get(route('taikhoan.edit'))->assertOk();
    }

    public function test_giang_vien_khong_duoc_vao_quan_ly_menu_va_nguoi_dung(): void
    {
        $this->actingAs(User::factory()->giangVien()->create());

        $this->get(route('menu.index'))->assertForbidden();
        $this->get(route('menu.create'))->assertForbidden();
        $this->post(route('menu.store'), [])->assertForbidden();
        $this->get(route('nguoidung.index'))->assertForbidden();
        $this->post(route('nguoidung.store'), [])->assertForbidden();
    }

    public function test_admin_duoc_vao_tat_ca_chuc_nang(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        foreach (['dashboard', 'lophoc.index', 'sinhvien.index', 'menu.index', 'nguoidung.index', 'taikhoan.edit'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_sidebar_an_menu_chi_admin_voi_giang_vien(): void
    {
        Menu::factory()->create(['ten' => 'Menu Chung', 'url' => '/sinhvien', 'vi_tri' => 'sidebar']);
        Menu::factory()->chiAdmin()->create(['ten' => 'Menu Quản Trị', 'url' => '/nguoidung', 'vi_tri' => 'sidebar']);

        $this->actingAs(User::factory()->giangVien()->create());
        $this->get(route('dashboard'))->assertSee('Menu Chung')->assertDontSee('Menu Quản Trị');

        $this->actingAs(User::factory()->admin()->create());
        $this->get(route('dashboard'))->assertSee('Menu Chung')->assertSee('Menu Quản Trị');
    }
}
