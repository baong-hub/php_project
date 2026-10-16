<?php

namespace Tests\Feature;

use App\Models\LopHoc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LopHocTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Các trang quản lý yêu cầu đăng nhập
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_can_display_lop_hoc_index_page(): void
    {
        LopHoc::create([
            'ten_lop' => 'Lớp 12A1',
            'ma_lop' => '12A1',
            'giao_vien' => 'Nguyễn Văn A',
            'si_so' => 30,
            'trang_thai' => true,
        ]);

        $response = $this->get(route('lophoc.index'));

        $response->assertStatus(200);
        $response->assertSee('Lớp 12A1');
        $response->assertSee('12A1');
    }

    public function test_can_filter_lop_hoc_by_search_keyword(): void
    {
        LopHoc::create([
            'ten_lop' => 'Lớp Công Nghệ Thông Tin',
            'ma_lop' => 'CNTT01',
            'giao_vien' => 'Trần Văn B',
            'si_so' => 40,
            'trang_thai' => true,
        ]);

        LopHoc::create([
            'ten_lop' => 'Lớp Kế Toán',
            'ma_lop' => 'KT01',
            'giao_vien' => 'Lê Thị C',
            'si_so' => 25,
            'trang_thai' => true,
        ]);

        $response = $this->get(route('lophoc.index', ['search' => 'CNTT01']));

        $response->assertStatus(200);
        $response->assertSee('Lớp Công Nghệ Thông Tin');
        $response->assertDontSee('Lớp Kế Toán');
    }

    public function test_can_render_create_lop_hoc_page(): void
    {
        $response = $this->get(route('lophoc.create'));

        $response->assertStatus(200);
        $response->assertSee('Thêm lớp học');
    }

    public function test_can_store_new_lop_hoc(): void
    {
        $giangVien = User::factory()->giangVien()->create(['name' => 'Phạm Văn D']);

        $payload = [
            'ten_lop' => 'Lớp Điện Tử 01',
            'ma_lop' => 'DT01',
            'giang_vien_id' => $giangVien->id,
            'si_so' => 35,
            'trang_thai' => '1',
        ];

        $response = $this->post(route('lophoc.store'), $payload);

        $response->assertRedirect(route('lophoc.index'));
        $this->assertDatabaseHas('lop_hocs', [
            'ma_lop' => 'DT01',
            'ten_lop' => 'Lớp Điện Tử 01',
            'giang_vien_id' => $giangVien->id,
            'giao_vien' => 'Phạm Văn D',
        ]);
    }

    public function test_cannot_store_duplicate_ma_lop(): void
    {
        LopHoc::create([
            'ten_lop' => 'Lớp Gốc',
            'ma_lop' => 'DUPLICATE',
            'giao_vien' => 'Thầy A',
            'si_so' => 30,
            'trang_thai' => true,
        ]);

        $payload = [
            'ten_lop' => 'Lớp Trùng',
            'ma_lop' => 'DUPLICATE',
            'giao_vien' => 'Thầy B',
            'si_so' => 25,
            'trang_thai' => '1',
        ];

        $response = $this->post(route('lophoc.store'), $payload);

        $response->assertSessionHasErrors(['ma_lop']);
    }

    public function test_can_update_existing_lop_hoc(): void
    {
        $lopHoc = LopHoc::create([
            'ten_lop' => 'Lớp Cũ',
            'ma_lop' => 'OLD01',
            'giao_vien' => 'Cô E',
            'si_so' => 20,
            'trang_thai' => true,
        ]);

        $payload = [
            'ten_lop' => 'Lớp Mới Cập Nhật',
            'ma_lop' => 'OLD01',
            'giang_vien_id' => User::factory()->giangVien()->create()->id,
            'si_so' => 28,
            'trang_thai' => '1',
        ];

        $response = $this->put(route('lophoc.update', $lopHoc->id), $payload);

        $response->assertRedirect(route('lophoc.index'));
        $this->assertDatabaseHas('lop_hocs', [
            'id' => $lopHoc->id,
            'ten_lop' => 'Lớp Mới Cập Nhật',
            'si_so' => 28,
        ]);
    }

    public function test_can_delete_lop_hoc(): void
    {
        $lopHoc = LopHoc::create([
            'ten_lop' => 'Lớp Cần Xóa',
            'ma_lop' => 'DEL01',
            'giao_vien' => 'Thầy F',
            'si_so' => 15,
            'trang_thai' => true,
        ]);

        $response = $this->delete(route('lophoc.destroy', $lopHoc->id));

        $this->assertDatabaseMissing('lop_hocs', [
            'id' => $lopHoc->id,
        ]);
    }
}
