<?php

namespace Tests\Feature;

use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SinhVienTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Các trang quản lý yêu cầu đăng nhập
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_can_display_sinh_vien_index_page(): void
    {
        $lopHoc = LopHoc::create([
            'ten_lop' => 'Lớp 10A',
            'ma_lop' => '10A',
            'giao_vien' => 'Thầy H',
            'si_so' => 30,
            'trang_thai' => true,
        ]);

        SinhVien::create([
            'ma_sv' => 'SV0001',
            'ho_ten' => 'Nguyễn Văn Nam',
            'email' => 'nam.nv@example.com',
            'ngay_sinh' => '2002-05-15',
            'gioi_tinh' => true,
            'lop_hoc_id' => $lopHoc->id,
            'so_dien_thoai' => '0987654321',
            'dia_chi' => 'Hà Nội',
            'trang_thai' => true,
        ]);

        $response = $this->get(route('sinhvien.index'));

        $response->assertStatus(200);
        $response->assertSee('Nguyễn Văn Nam');
        $response->assertSee('SV0001');
    }

    public function test_can_filter_sinh_vien_by_search_keyword(): void
    {
        $lopHoc = LopHoc::create([
            'ten_lop' => 'Lớp 10B',
            'ma_lop' => '10B',
            'giao_vien' => 'Cô K',
            'si_so' => 25,
            'trang_thai' => true,
        ]);

        SinhVien::create([
            'ma_sv' => 'SV0002',
            'ho_ten' => 'Trần Thị Hoa',
            'email' => 'hoa.tt@example.com',
            'gioi_tinh' => false,
            'lop_hoc_id' => $lopHoc->id,
            'trang_thai' => true,
        ]);

        SinhVien::create([
            'ma_sv' => 'SV0003',
            'ho_ten' => 'Lê Tuấn Anh',
            'email' => 'anh.lt@example.com',
            'gioi_tinh' => true,
            'lop_hoc_id' => $lopHoc->id,
            'trang_thai' => true,
        ]);

        $response = $this->get(route('sinhvien.index', ['search' => 'Hoa']));

        $response->assertStatus(200);
        $response->assertSee('Trần Thị Hoa');
        $response->assertDontSee('Lê Tuấn Anh');
    }

    public function test_can_render_create_sinh_vien_page(): void
    {
        $response = $this->get(route('sinhvien.create'));

        $response->assertStatus(200);
        $response->assertSee('Thêm sinh viên');
    }

    public function test_can_store_new_sinh_vien(): void
    {
        $lopHoc = LopHoc::create([
            'ten_lop' => 'Lớp 11C',
            'ma_lop' => '11C',
            'giao_vien' => 'Thầy M',
            'si_so' => 30,
            'trang_thai' => true,
        ]);

        $payload = [
            'ma_sv' => 'SV0099',
            'ho_ten' => 'Phạm Hoàng Long',
            'email' => 'long.ph@example.com',
            'ngay_sinh' => '2003-08-20',
            'gioi_tinh' => '1',
            'lop_hoc_id' => $lopHoc->id,
            'so_dien_thoai' => '0912345678',
            'dia_chi' => 'Đà Nẵng',
            'trang_thai' => '1',
        ];

        $response = $this->post(route('sinhvien.store'), $payload);

        $response->assertRedirect(route('sinhvien.index'));
        $this->assertDatabaseHas('sinh_viens', [
            'ma_sv' => 'SV0099',
            'ho_ten' => 'Phạm Hoàng Long',
            'email' => 'long.ph@example.com',
        ]);
    }

    public function test_cannot_store_duplicate_email_or_ma_sv(): void
    {
        $lopHoc = LopHoc::create([
            'ten_lop' => 'Lớp 11D',
            'ma_lop' => '11D',
            'giao_vien' => 'Cô N',
            'si_so' => 20,
            'trang_thai' => true,
        ]);

        SinhVien::create([
            'ma_sv' => 'SVEXIST',
            'ho_ten' => 'Sinh Viên Gốc',
            'email' => 'exist@example.com',
            'gioi_tinh' => true,
            'lop_hoc_id' => $lopHoc->id,
            'trang_thai' => true,
        ]);

        $payload = [
            'ma_sv' => 'SVEXIST',
            'ho_ten' => 'Sinh Viên Mới',
            'email' => 'exist@example.com',
            'gioi_tinh' => '1',
            'lop_hoc_id' => $lopHoc->id,
            'trang_thai' => '1',
        ];

        $response = $this->post(route('sinhvien.store'), $payload);

        $response->assertSessionHasErrors(['ma_sv', 'email']);
    }

    public function test_can_update_sinh_vien(): void
    {
        $lopHoc = LopHoc::create([
            'ten_lop' => 'Lớp 12B',
            'ma_lop' => '12B',
            'giao_vien' => 'Thầy P',
            'si_so' => 30,
            'trang_thai' => true,
        ]);

        $sinhVien = SinhVien::create([
            'ma_sv' => 'SV0055',
            'ho_ten' => 'Vũ Quốc Huy',
            'email' => 'huy.vq@example.com',
            'gioi_tinh' => true,
            'lop_hoc_id' => $lopHoc->id,
            'trang_thai' => true,
        ]);

        $payload = [
            'ma_sv' => 'SV0055',
            'ho_ten' => 'Vũ Quốc Huy (Đã sửa)',
            'email' => 'huy.vq@example.com',
            'gioi_tinh' => '1',
            'lop_hoc_id' => $lopHoc->id,
            'trang_thai' => '1',
        ];

        $response = $this->put(route('sinhvien.update', $sinhVien->id), $payload);

        $response->assertRedirect(route('sinhvien.index'));
        $this->assertDatabaseHas('sinh_viens', [
            'id' => $sinhVien->id,
            'ho_ten' => 'Vũ Quốc Huy (Đã sửa)',
        ]);
    }

    public function test_can_delete_sinh_vien(): void
    {
        $sinhVien = SinhVien::create([
            'ma_sv' => 'SVDEL',
            'ho_ten' => 'Sinh Viên Xóa',
            'email' => 'del@example.com',
            'gioi_tinh' => true,
            'trang_thai' => true,
        ]);

        $response = $this->delete(route('sinhvien.destroy', $sinhVien->id));

        $response->assertRedirect(route('sinhvien.index'));
        $this->assertDatabaseMissing('sinh_viens', [
            'id' => $sinhVien->id,
        ]);
    }
}
