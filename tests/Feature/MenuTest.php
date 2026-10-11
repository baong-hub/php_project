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
