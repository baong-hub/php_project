<?php

namespace Tests\Feature;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_display_menu_index_page(): void
    {
        Menu::create([
            'ten' => 'Trang chủ',
            'url' => '/',
            'vi_tri' => 'header',
            'nhom' => 'Chính',
            'thu_tu' => 1,
            'trang_thai' => true,
        ]);

        $response = $this->get(route('menu.index'));

        $response->assertStatus(200);
        $response->assertSee('Trang chủ');
    }

    /**
     * Form lọc gửi kèm các ô để trống (search=&trang_thai=...), các ô này
     * phải được bỏ qua chứ không lọc theo giá trị rỗng/null.
     */
    public function test_loc_menu_bo_qua_o_trong(): void
    {
        $this->taoMenuMau();

        $this->assertKetQuaLoc(['search' => '', 'vi_tri' => 'sidebar', 'trang_thai' => ''], ['Sidebar Bật', 'Sidebar Tắt']);
        $this->assertKetQuaLoc(['search' => '', 'vi_tri' => '', 'trang_thai' => '1'], ['Sidebar Bật', 'Header Bật']);
        $this->assertKetQuaLoc(['search' => '', 'vi_tri' => '', 'trang_thai' => '0'], ['Sidebar Tắt']);
        $this->assertKetQuaLoc(['search' => 'header', 'vi_tri' => '', 'trang_thai' => ''], ['Header Bật']);
        $this->assertKetQuaLoc(['search' => '', 'vi_tri' => 'header', 'trang_thai' => '1'], ['Header Bật']);
        $this->assertKetQuaLoc(['search' => '', 'vi_tri' => '', 'trang_thai' => ''], ['Sidebar Bật', 'Sidebar Tắt', 'Header Bật']);
    }

    public function test_loc_menu_voi_tham_so_khong_hop_le_khong_loi(): void
    {
        $this->taoMenuMau();

        $tatCa = ['Sidebar Bật', 'Sidebar Tắt', 'Header Bật'];

        $this->assertKetQuaLoc(['per_page' => 'abc'], $tatCa);
        $this->assertKetQuaLoc(['per_page' => '-5'], $tatCa);
        $this->assertKetQuaLoc(['per_page' => '100000', 'vi_tri' => 'xyz', 'trang_thai' => '9'], $tatCa);
        $this->assertKetQuaLoc(['sort_by' => 'xyz', 'sort_dir' => 'hack'], $tatCa);
    }

    private function assertKetQuaLoc(array $query, array $tenMongDoi): void
    {
        $this->get(route('menu.index', $query))
            ->assertOk()
            ->assertViewHas('menus', function ($menus) use ($tenMongDoi) {
                return $menus->pluck('ten')->sort()->values()->all() === collect($tenMongDoi)->sort()->values()->all();
            });
    }

    private function taoMenuMau(): void
    {
        Menu::create(['ten' => 'Sidebar Bật', 'url' => '/a', 'vi_tri' => 'sidebar', 'thu_tu' => 1, 'trang_thai' => true]);
        Menu::create(['ten' => 'Sidebar Tắt', 'url' => '/b', 'vi_tri' => 'sidebar', 'thu_tu' => 2, 'trang_thai' => false]);
        Menu::create(['ten' => 'Header Bật', 'url' => '/header', 'vi_tri' => 'header', 'thu_tu' => 3, 'trang_thai' => true]);
    }

    public function test_can_store_new_menu(): void
    {
        $payload = [
            'ten' => 'Danh sách Lớp học',
            'url' => '/lophoc',
            'vi_tri' => 'sidebar',
            'nhom' => 'Quản lý',
            'thu_tu' => 2,
            'trang_thai' => '1',
        ];

        $response = $this->post(route('menu.store'), $payload);

        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseHas('menus', [
            'ten' => 'Danh sách Lớp học',
            'url' => '/lophoc',
            'vi_tri' => 'sidebar',
        ]);
    }

    public function test_cannot_store_menu_with_invalid_url(): void
    {
        $payload = [
            'ten' => 'Menu Sai URL',
            'url' => 'invalid-path-without-slash',
            'vi_tri' => 'header',
            'thu_tu' => 1,
            'trang_thai' => '1',
        ];

        $response = $this->post(route('menu.store'), $payload);

        $response->assertSessionHasErrors(['url']);
    }

    public function test_can_update_menu(): void
    {
        $menu = Menu::create([
            'ten' => 'Menu Cũ',
            'url' => '/cu',
            'vi_tri' => 'header',
            'thu_tu' => 1,
            'trang_thai' => true,
        ]);

        $payload = [
            'ten' => 'Menu Mới',
            'url' => '/moi',
            'vi_tri' => 'sidebar',
            'thu_tu' => 5,
            'trang_thai' => '1',
        ];

        $response = $this->put(route('menu.update', $menu->id), $payload);

        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'ten' => 'Menu Mới',
            'url' => '/moi',
        ]);
    }

    public function test_sidebar_hien_thi_menu_dang_bat_theo_nhom(): void
    {
        Menu::create([
            'ten' => 'Báo cáo',
            'icon' => 'chart',
            'url' => '/baocao',
            'vi_tri' => 'sidebar',
            'nhom' => 'Thống kê',
            'thu_tu' => 1,
            'trang_thai' => true,
        ]);
        Menu::create([
            'ten' => 'Menu Bị Ẩn',
            'url' => '/an',
            'vi_tri' => 'sidebar',
            'thu_tu' => 2,
            'trang_thai' => false,
        ]);

        $response = $this->get(route('sinhvien.index'));

        $response->assertStatus(200);
        $response->assertSee('Thống kê');
        $response->assertSee('href="' . url('/baocao') . '"', false);
        $response->assertDontSee('Menu Bị Ẩn');
    }

    public function test_header_hien_thi_menu_vi_tri_header(): void
    {
        Menu::create([
            'ten' => 'Trợ giúp',
            'url' => '/tro-giup',
            'vi_tri' => 'header',
            'thu_tu' => 1,
            'trang_thai' => true,
        ]);

        $response = $this->get(route('sinhvien.index'));

        $response->assertSee('class="header-nav"', false);
        $response->assertSee('Trợ giúp');
    }

    public function test_cannot_store_menu_with_invalid_icon(): void
    {
        $response = $this->post(route('menu.store'), [
            'ten' => 'Menu',
            'icon' => 'khong-ton-tai',
            'url' => '/abc',
            'vi_tri' => 'sidebar',
            'thu_tu' => 1,
            'trang_thai' => '1',
        ]);

        $response->assertSessionHasErrors(['icon']);
    }

    public function test_can_delete_menu(): void
    {
        $menu = Menu::create([
            'ten' => 'Menu Cần Xóa',
            'url' => '/delete',
            'vi_tri' => 'header',
            'thu_tu' => 1,
            'trang_thai' => true,
        ]);

        $response = $this->delete(route('menu.destroy', $menu->id));

        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseMissing('menus', [
            'id' => $menu->id,
        ]);
    }
}
