<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_giang_vien_cap_nhat_thong_tin_ca_nhan(): void
    {
        $user = User::factory()->giangVien()->create();
        $this->actingAs($user);

        $this->put(route('taikhoan.update'), [
            'name' => 'Tên Đã Đổi',
            'email' => 'doi@test.vn',
            // Gửi kèm role để chắc chắn không tự nâng quyền được
            'role' => 'admin',
        ])->assertRedirect(route('taikhoan.edit'));

        $user->refresh();
        $this->assertSame('Tên Đã Đổi', $user->name);
        $this->assertSame('doi@test.vn', $user->email);
        $this->assertSame('giang_vien', $user->role);
    }

    public function test_doi_mat_khau_can_mat_khau_hien_tai_dung(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->put(route('taikhoan.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'matkhaumoi1',
            'password_confirmation' => 'matkhaumoi1',
        ])->assertSessionHasErrors('current_password');

        $this->put(route('taikhoan.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'sai',
            'password' => 'matkhaumoi1',
            'password_confirmation' => 'matkhaumoi1',
        ])->assertSessionHasErrors('current_password');

        $this->put(route('taikhoan.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password',
            'password' => 'matkhaumoi1',
            'password_confirmation' => 'matkhaumoi1',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('matkhaumoi1', $user->fresh()->password));
    }
}
