<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_khach_bi_chuyen_ve_trang_dang_nhap(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('sinhvien.index'))->assertRedirect(route('login'));
    }

    public function test_trang_dang_nhap_hien_thi(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Đăng nhập')
            ->assertDontSee('<aside>', false);
    }

    public function test_dang_nhap_thanh_cong(): void
    {
        $user = User::factory()->create(['email' => 'gv@test.vn']);

        $this->post(route('login.store'), ['email' => 'gv@test.vn', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_dang_nhap_sai_mat_khau(): void
    {
        User::factory()->create(['email' => 'gv@test.vn']);

        $this->from(route('login'))
            ->post(route('login.store'), ['email' => 'gv@test.vn', 'password' => 'sai-mat-khau'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_dang_nhap_validate_bang_request(): void
    {
        $this->post(route('login.store'), ['email' => 'khong-phai-email', 'password' => ''])
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_tai_khoan_bi_khoa_khong_dang_nhap_duoc(): void
    {
        User::factory()->biKhoa()->create(['email' => 'khoa@test.vn']);

        $this->post(route('login.store'), ['email' => 'khoa@test.vn', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_tai_khoan_bi_khoa_khi_dang_dang_nhap_bi_dang_xuat(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->update(['trang_thai' => false]);

        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_dang_xuat(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_da_dang_nhap_vao_trang_dang_nhap_thi_ve_dashboard(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('login'))->assertRedirect(route('dashboard'));
    }
}
