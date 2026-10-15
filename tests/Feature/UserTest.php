<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create(['name' => 'Admin Chính']);
        $this->actingAs($this->admin);
    }

    public function test_hien_thi_danh_sach_nguoi_dung(): void
    {
        User::factory()->create(['name' => 'Giảng Viên A']);

        $this->get(route('nguoidung.index'))
            ->assertOk()
            ->assertSee('Giảng Viên A')
            ->assertSee('Admin Chính');
    }

    public function test_loc_sap_xep_phan_trang(): void
    {
        User::factory()->create(['name' => 'Trần Lọc', 'email' => 'loc@test.vn']);
        User::factory()->biKhoa()->create(['name' => 'Người Bị Khóa']);
        User::factory()->count(12)->create();

        $ten = fn ($query) => $this->get(route('nguoidung.index', $query))
            ->assertOk()->viewData('users')->pluck('name')->all();

        $this->assertSame(['Trần Lọc'], $ten(['search' => 'loc@test', 'role' => '', 'trang_thai' => '']));
        $this->assertSame(['Admin Chính'], $ten(['role' => 'admin']));
        $this->assertSame(['Người Bị Khóa'], $ten(['trang_thai' => '0']));
        $tangDan = $ten(['sort_by' => 'name', 'sort_dir' => 'asc', 'per_page' => 50]);
        $giamDan = $ten(['sort_by' => 'name', 'sort_dir' => 'desc', 'per_page' => 50]);
        $this->assertCount(15, $tangDan);
        $this->assertSame(array_reverse($tangDan), $giamDan);

        $page = $this->get(route('nguoidung.index'))->viewData('users');
        $this->assertSame(10, $page->count());
        $this->assertSame(15, $page->total());
    }

    public function test_them_nguoi_dung(): void
    {
        $this->post(route('nguoidung.store'), [
            'name' => 'Giảng Viên Mới',
            'email' => 'moi@test.vn',
            'role' => 'giang_vien',
            'trang_thai' => '1',
            'password' => 'matkhau123',
            'password_confirmation' => 'matkhau123',
        ])->assertRedirect(route('nguoidung.index'));

        $user = User::where('email', 'moi@test.vn')->firstOrFail();
        $this->assertSame('giang_vien', $user->role);
        $this->assertTrue(Hash::check('matkhau123', $user->password));
    }

    public function test_them_nguoi_dung_validate(): void
    {
        $this->post(route('nguoidung.store'), [
            'name' => '',
            'email' => $this->admin->email,
            'role' => 'hacker',
            'password' => '123',
            'password_confirmation' => '456',
        ])->assertSessionHasErrors(['name', 'email', 'role', 'password']);
    }

    public function test_sua_nguoi_dung_de_trong_mat_khau_thi_giu_nguyen(): void
    {
        $user = User::factory()->create();
        $matKhauCu = $user->password;

        $this->put(route('nguoidung.update', $user->id), [
            'name' => 'Tên Mới',
            'email' => $user->email,
            'role' => 'admin',
            'trang_thai' => '1',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('nguoidung.index'));

        $user->refresh();
        $this->assertSame('Tên Mới', $user->name);
        $this->assertSame('admin', $user->role);
        $this->assertSame($matKhauCu, $user->password);
    }

    public function test_admin_khong_the_tu_ha_quyen_hoac_tu_khoa(): void
    {
        $this->put(route('nguoidung.update', $this->admin->id), [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'role' => 'giang_vien',
            'password' => '',
        ])->assertSessionHasErrors(['role', 'trang_thai']);

        $this->assertTrue($this->admin->fresh()->isAdmin());
    }

    public function test_xoa_nguoi_dung(): void
    {
        $user = User::factory()->create();

        $this->delete(route('nguoidung.destroy', $user->id))->assertRedirect(route('nguoidung.index'));
        $this->assertModelMissing($user);
    }

    public function test_khong_the_tu_xoa_chinh_minh(): void
    {
        $this->delete(route('nguoidung.destroy', $this->admin->id))->assertSessionHas('error');
        $this->assertModelExists($this->admin);
    }
}
