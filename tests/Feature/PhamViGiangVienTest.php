<?php

namespace Tests\Feature;

use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Giảng viên chỉ quản lý lớp mình chủ nhiệm và sinh viên của các lớp đó.
 */
class PhamViGiangVienTest extends TestCase
{
    use RefreshDatabase;

    private User $gv;
    private User $gvKhac;
    private LopHoc $lopCuaToi;
    private LopHoc $lopNguoiKhac;
    private SinhVien $svCuaToi;
    private SinhVien $svNguoiKhac;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gv = User::factory()->giangVien()->create(['name' => 'Cô Chủ Nhiệm']);
        $this->gvKhac = User::factory()->giangVien()->create(['name' => 'Thầy Khác']);

        $this->lopCuaToi = LopHoc::factory()->cuaGiangVien($this->gv)->create(['ten_lop' => 'Lớp Của Tôi', 'trang_thai' => true]);
        $this->lopNguoiKhac = LopHoc::factory()->cuaGiangVien($this->gvKhac)->create(['ten_lop' => 'Lớp Người Khác', 'trang_thai' => true]);

        $this->svCuaToi = SinhVien::factory()->create(['ho_ten' => 'Sinh Viên Của Tôi', 'lop_hoc_id' => $this->lopCuaToi->id]);
        $this->svNguoiKhac = SinhVien::factory()->create(['ho_ten' => 'Sinh Viên Người Khác', 'lop_hoc_id' => $this->lopNguoiKhac->id]);
    }

    public function test_ten_giao_vien_tu_dong_theo_tai_khoan(): void
    {
        $this->assertSame('Cô Chủ Nhiệm', $this->lopCuaToi->giao_vien);

        $this->gv->update(['name' => 'Cô Đã Đổi Tên']);

        $this->assertSame('Cô Đã Đổi Tên', $this->lopCuaToi->fresh()->giao_vien);
    }

    public function test_danh_sach_chi_hien_lop_va_sinh_vien_cua_minh(): void
    {
        $this->actingAs($this->gv);

        $lops = $this->get(route('lophoc.index'))->assertOk()->viewData('lophocs');
        $this->assertSame(['Lớp Của Tôi'], $lops->pluck('ten_lop')->all());

        $svs = $this->get(route('sinhvien.index'))->assertOk()->viewData('sinhviens');
        $this->assertSame(['Sinh Viên Của Tôi'], $svs->pluck('ho_ten')->all());

        // Ô chọn lớp trong bộ lọc sinh viên cũng chỉ có lớp của mình
        $this->get(route('sinhvien.index'))->assertDontSee('Lớp Người Khác');
    }

    public function test_khong_xem_sua_xoa_duoc_lop_cua_nguoi_khac(): void
    {
        $this->actingAs($this->gv);
        $id = $this->lopNguoiKhac->id;

        $this->get(route('lophoc.show', $id))->assertNotFound();
        $this->get(route('lophoc.edit', $id))->assertNotFound();
        $this->put(route('lophoc.update', $id), [
            'ten_lop' => 'Chiếm lớp', 'ma_lop' => $this->lopNguoiKhac->ma_lop, 'si_so' => 10, 'trang_thai' => '1',
        ])->assertNotFound();
        $this->delete(route('lophoc.destroy', $id))->assertForbidden();

        $this->assertModelExists($this->lopNguoiKhac);
        $this->assertSame('Lớp Người Khác', $this->lopNguoiKhac->fresh()->ten_lop);
    }

    public function test_khong_xem_sua_xoa_duoc_sinh_vien_cua_nguoi_khac(): void
    {
        $this->actingAs($this->gv);
        $id = $this->svNguoiKhac->id;

        $this->get(route('sinhvien.show', $id))->assertNotFound();
        $this->get(route('sinhvien.edit', $id))->assertNotFound();
        $this->delete(route('sinhvien.destroy', $id))->assertNotFound();

        $this->assertModelExists($this->svNguoiKhac);
    }

    public function test_giang_vien_khong_duoc_xoa_lop_ke_ca_lop_cua_minh(): void
    {
        $this->actingAs($this->gv);

        $this->get(route('lophoc.index'))->assertDontSee('action="' . route('lophoc.destroy', $this->lopCuaToi->id) . '"', false);
        $this->delete(route('lophoc.destroy', $this->lopCuaToi->id))->assertForbidden();

        $this->travelTo(now()->setTime(10, 0));
        Sanctum::actingAs($this->gv);
        $this->deleteJson("/api/lop-hoc/{$this->lopCuaToi->id}")->assertForbidden();

        $this->assertModelExists($this->lopCuaToi);
        $this->assertSame($this->lopCuaToi->id, $this->svCuaToi->fresh()->lop_hoc_id);
    }

    public function test_admin_xoa_duoc_lop(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('lophoc.index'))->assertSee('action="' . route('lophoc.destroy', $this->lopCuaToi->id) . '"', false);
        $this->delete(route('lophoc.destroy', $this->lopCuaToi->id))->assertRedirect();

        $this->assertModelMissing($this->lopCuaToi);
    }

    public function test_tao_lop_tu_gan_cho_ban_than_khong_gan_duoc_cho_nguoi_khac(): void
    {
        $this->actingAs($this->gv);

        $this->post(route('lophoc.store'), [
            'ten_lop' => 'Lớp Mới Mở',
            'ma_lop' => 'MOI01',
            'giang_vien_id' => $this->gvKhac->id, // cố gán cho người khác
            'si_so' => 30,
            'trang_thai' => '1',
        ])->assertRedirect(route('lophoc.index'));

        $lop = LopHoc::where('ma_lop', 'MOI01')->firstOrFail();
        $this->assertSame($this->gv->id, $lop->giang_vien_id);
        $this->assertSame('Cô Chủ Nhiệm', $lop->giao_vien);
    }

    public function test_sua_lop_cua_minh_khong_chuyen_duoc_cho_nguoi_khac(): void
    {
        $this->actingAs($this->gv);

        $this->put(route('lophoc.update', $this->lopCuaToi->id), [
            'ten_lop' => 'Lớp Của Tôi (sửa)',
            'ma_lop' => $this->lopCuaToi->ma_lop,
            'giang_vien_id' => $this->gvKhac->id,
            'si_so' => 30,
            'trang_thai' => '1',
        ])->assertRedirect(route('lophoc.index'));

        $lop = $this->lopCuaToi->fresh();
        $this->assertSame('Lớp Của Tôi (sửa)', $lop->ten_lop);
        $this->assertSame($this->gv->id, $lop->giang_vien_id);
    }

    public function test_them_sinh_vien_phai_vao_lop_cua_minh(): void
    {
        $this->actingAs($this->gv);
        $payload = [
            'ma_sv' => 'SVGV01',
            'ho_ten' => 'Sinh Viên Mới',
            'email' => 'svgv01@test.vn',
            'gioi_tinh' => '1',
            'trang_thai' => '1',
        ];

        // Không chọn lớp -> lỗi (sẽ nằm ngoài phạm vi quản lý)
        $this->post(route('sinhvien.store'), $payload)->assertSessionHasErrors('lop_hoc_id');

        // Lớp của người khác -> lỗi
        $this->post(route('sinhvien.store'), $payload + ['lop_hoc_id' => $this->lopNguoiKhac->id])
            ->assertSessionHasErrors('lop_hoc_id');

        // Lớp của mình -> được
        $this->post(route('sinhvien.store'), $payload + ['lop_hoc_id' => $this->lopCuaToi->id])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sinh_viens', ['ma_sv' => 'SVGV01', 'lop_hoc_id' => $this->lopCuaToi->id]);
    }

    public function test_khong_chuyen_sinh_vien_sang_lop_nguoi_khac(): void
    {
        $this->actingAs($this->gv);

        $this->put(route('sinhvien.update', $this->svCuaToi->id), [
            'ma_sv' => $this->svCuaToi->ma_sv,
            'ho_ten' => $this->svCuaToi->ho_ten,
            'email' => $this->svCuaToi->email,
            'gioi_tinh' => '1',
            'trang_thai' => '1',
            'lop_hoc_id' => $this->lopNguoiKhac->id,
        ])->assertSessionHasErrors('lop_hoc_id');

        $this->assertSame($this->lopCuaToi->id, $this->svCuaToi->fresh()->lop_hoc_id);
    }

    public function test_dashboard_chi_tinh_so_lieu_cua_minh(): void
    {
        $this->actingAs($this->gv);

        $thongKe = $this->get(route('dashboard'))->assertOk()->viewData('thongKe');
        $this->assertSame(1, $thongKe['sinhVien']);
        $this->assertSame(1, $thongKe['lopHoc']);

        $this->get(route('dashboard'))->assertDontSee('Sinh Viên Người Khác')->assertDontSee('Lớp Người Khác');
    }

    public function test_admin_thay_tat_ca(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->assertSame(2, $this->get(route('lophoc.index'))->viewData('lophocs')->total());
        $this->assertSame(2, $this->get(route('sinhvien.index'))->viewData('sinhviens')->total());
        $this->get(route('lophoc.show', $this->lopNguoiKhac->id))->assertOk();
    }

    public function test_api_cung_gioi_han_pham_vi(): void
    {
        $this->travelTo(now()->setTime(10, 0));
        Sanctum::actingAs($this->gv);

        $this->getJson('/api/lop-hoc')->assertOk()->assertJsonPath('meta.total', 1);
        $this->getJson('/api/sinh-vien')->assertOk()->assertJsonPath('meta.total', 1);
        $this->getJson("/api/lop-hoc/{$this->lopNguoiKhac->id}")->assertNotFound();
        $this->getJson("/api/sinh-vien/{$this->svNguoiKhac->id}")->assertNotFound();
        $this->deleteJson("/api/sinh-vien/{$this->svNguoiKhac->id}")->assertNotFound();

        $this->assertModelExists($this->svNguoiKhac);
    }
}
