<?php

namespace Tests\Feature;

use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // API chỉ mở trong giờ hành chính (middleware gio.hanh.chinh) -> cố định 10h sáng
        $this->travelTo(now()->setTime(10, 0));
    }

    private function dangNhapApi(?User $user = null): User
    {
        $user ??= User::factory()->giangVien()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    // ===== Xác thực =====

    public function test_login_tra_ve_token_va_dung_token_goi_api(): void
    {
        User::factory()->create(['email' => 'api@test.vn']);

        $token = $this->postJson('/api/login', ['email' => 'api@test.vn', 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('data.user.email', 'api@test.vn')
            ->assertJsonMissingPath('data.user.password')
            ->json('data.token');

        $this->withToken($token)->getJson('/api/lop-hoc')->assertOk();
    }

    public function test_login_sai_mat_khau(): void
    {
        User::factory()->create(['email' => 'api@test.vn']);

        $this->postJson('/api/login', ['email' => 'api@test.vn', 'password' => 'sai'])
            ->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_chua_co_token_bi_tu_choi(): void
    {
        $this->getJson('/api/lop-hoc')->assertUnauthorized();
        $this->getJson('/api/sinh-vien')->assertUnauthorized();
        // Không gửi header Accept JSON vẫn phải nhận JSON 401, không bị redirect
        $this->get('/api/lop-hoc')->assertUnauthorized();
    }

    public function test_ngoai_gio_hanh_chinh_bi_chan(): void
    {
        $this->dangNhapApi();
        $this->travelTo(now()->setTime(20, 0));

        $this->getJson('/api/lop-hoc')
            ->assertStatus(400)
            ->assertJsonPath('message', 'Bạn chỉ được thực hiện các thao tác trong giờ hành chính');
    }

    public function test_api_user_khong_lo_mat_khau(): void
    {
        $this->dangNhapApi();

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.role', 'giang_vien')
            ->assertJsonMissingPath('data.password');
    }

    // ===== Lớp học =====

    public function test_lop_hoc_index_loc_sap_xep_phan_trang(): void
    {
        $this->dangNhapApi();
        LopHoc::factory()->create(['ma_lop' => 'TIM01', 'ten_lop' => 'Lớp Cần Tìm', 'trang_thai' => true]);
        LopHoc::factory()->count(14)->create(['trang_thai' => false]);

        $this->getJson('/api/lop-hoc?search=TIM01')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.ten_lop', 'Lớp Cần Tìm');

        $this->getJson('/api/lop-hoc?trang_thai=1')->assertJsonCount(1, 'data');

        $this->getJson('/api/lop-hoc?per_page=5&page=2')
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.total', 15)
            ->assertJsonPath('meta.current_page', 2);

        $this->getJson('/api/lop-hoc?sort_by=si_so&sort_dir=hack')->assertUnprocessable();
    }

    public function test_lop_hoc_crud(): void
    {
        $this->dangNhapApi();

        $id = $this->postJson('/api/lop-hoc', [
            'ma_lop' => 'API01',
            'ten_lop' => 'Lớp API',
            'giao_vien' => 'Thầy API',
            'si_so' => 30,
            'trang_thai' => true,
        ])->assertCreated()->json('data.id');

        $this->getJson("/api/lop-hoc/{$id}")
            ->assertOk()
            ->assertJsonPath('data.ma_lop', 'API01')
            ->assertJsonPath('data.so_sinh_vien', 0);

        $this->putJson("/api/lop-hoc/{$id}", [
            'ma_lop' => 'API01',
            'ten_lop' => 'Lớp API Đã Sửa',
            'giao_vien' => 'Thầy API',
            'si_so' => 35,
            'trang_thai' => true,
        ])->assertOk()->assertJsonPath('data.ten_lop', 'Lớp API Đã Sửa');

        $this->deleteJson("/api/lop-hoc/{$id}")->assertOk();
        $this->getJson("/api/lop-hoc/{$id}")->assertNotFound();
    }

    public function test_lop_hoc_validate(): void
    {
        $this->dangNhapApi();
        LopHoc::factory()->create(['ma_lop' => 'TRUNG']);

        $this->postJson('/api/lop-hoc', ['ma_lop' => 'TRUNG', 'si_so' => 0])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ma_lop', 'ten_lop', 'giao_vien', 'si_so', 'trang_thai']);
    }

    public function test_api_cap_nhat_thieu_trang_thai_khong_ngam_tat(): void
    {
        $this->dangNhapApi();
        $lop = LopHoc::factory()->create(['trang_thai' => true]);

        $this->putJson("/api/lop-hoc/{$lop->id}", [
            'ma_lop' => $lop->ma_lop,
            'ten_lop' => 'Tên mới',
            'giao_vien' => $lop->giao_vien,
            'si_so' => 30,
        ])->assertJsonValidationErrors('trang_thai');

        $this->assertTrue((bool) $lop->fresh()->trang_thai);
    }

    // ===== Sinh viên =====

    public function test_sinh_vien_index_loc_theo_lop(): void
    {
        $this->dangNhapApi();
        $lop = LopHoc::factory()->create();
        SinhVien::factory()->count(3)->create(['lop_hoc_id' => $lop->id]);
        SinhVien::factory()->count(2)->create();

        $this->getJson("/api/sinh-vien?lop_hoc_id={$lop->id}")
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.lop_hoc.id', $lop->id);

        $this->getJson('/api/sinh-vien')->assertJsonPath('meta.total', 5);
    }

    public function test_sinh_vien_crud(): void
    {
        $this->dangNhapApi(User::factory()->admin()->create());
        $lop = LopHoc::factory()->create();

        $payload = [
            'ma_sv' => 'SVAPI01',
            'ho_ten' => 'Sinh Viên API',
            'email' => 'svapi@test.vn',
            'ngay_sinh' => '2004-05-06',
            'gioi_tinh' => true,
            'lop_hoc_id' => $lop->id,
            'trang_thai' => true,
        ];

        $id = $this->postJson('/api/sinh-vien', $payload)
            ->assertCreated()
            ->assertJsonPath('data.ngay_sinh', '2004-05-06')
            ->assertJsonPath('data.lop_hoc.ma_lop', $lop->ma_lop)
            ->json('data.id');

        $this->getJson("/api/sinh-vien/{$id}")->assertOk()->assertJsonPath('data.ma_sv', 'SVAPI01');

        $this->putJson("/api/sinh-vien/{$id}", ['ho_ten' => 'Tên Đã Sửa'] + $payload)
            ->assertOk()
            ->assertJsonPath('data.ho_ten', 'Tên Đã Sửa');

        $this->postJson('/api/sinh-vien', $payload)
            ->assertJsonValidationErrors(['ma_sv', 'email']);

        $this->deleteJson("/api/sinh-vien/{$id}")->assertOk();
        $this->assertDatabaseMissing('sinh_viens', ['id' => $id]);
    }

    public function test_tai_khoan_bi_khoa_khong_goi_duoc_api(): void
    {
        $this->dangNhapApi(User::factory()->biKhoa()->create());

        $this->getJson('/api/lop-hoc')->assertForbidden();
    }
}
