<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Menu extends Model
{
    use HasFactory;

    // Các vị trí hiển thị menu trên layout
    public const VI_TRI = [
        'header' => 'Header',
        'sidebar' => 'Sidebar',
    ];

    // Icon có thể chọn cho menu: khóa => [tên hiển thị, nội dung SVG]
    public const ICONS = [
        'dashboard' => ['Dashboard', '<rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/>'],
        'home' => ['Trang chủ', '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
        'user' => ['Người dùng', '<circle cx="12" cy="8" r="4"/><path d="M4 21v-1a8 8 0 0 1 16 0v1"/>'],
        'book' => ['Sách', '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>'],
        'check' => ['Đánh dấu', '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>'],
        'chart' => ['Biểu đồ', '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>'],
        'file' => ['Tài liệu', '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
        'calendar' => ['Lịch', '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>'],
        'menu' => ['Danh sách', '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>'],
        'settings' => ['Cài đặt', '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>'],
        'link' => ['Liên kết', '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>'],
    ];

    public const ICON_MAC_DINH = 'link';

    protected $fillable = [
        'ten',
        'icon',
        'url',
        'vi_tri',
        'nhom',
        'thu_tu',
        'trang_thai',
    ];

    /**
     * Các menu đang hiển thị ở một vị trí, sắp theo thứ tự.
     */
    public static function dangHienThi(string $viTri): Collection
    {
        return static::where('vi_tri', $viTri)
            ->where('trang_thai', true)
            ->orderBy('thu_tu')
            ->orderBy('id')
            ->get();
    }

    /**
     * Gom menu sidebar theo nhóm (không phân biệt hoa thường).
     * Nhóm xuất hiện theo thứ tự của menu đầu tiên trong nhóm.
     */
    public static function sidebarTheoNhom(): Collection
    {
        return static::dangHienThi('sidebar')
            ->groupBy(fn (Menu $menu) => mb_strtolower(trim((string) $menu->nhom)))
            ->map(fn (Collection $items) => [
                'nhom' => $items->first()->nhom ?: 'Khác',
                'items' => $items,
            ])
            ->values();
    }

    /**
     * Nội dung SVG của icon (dùng icon mặc định nếu chưa chọn).
     */
    public function iconSvg(): string
    {
        return (self::ICONS[$this->icon] ?? self::ICONS[self::ICON_MAC_DINH])[1];
    }

    /**
     * Menu này có trỏ tới trang đang xem không (để tô sáng).
     */
    public function dangChon(): bool
    {
        $path = trim((string) parse_url($this->url, PHP_URL_PATH), '/');

        if ($path === '') {
            return $this->url === '/' && request()->is('/');
        }

        return request()->is($path, $path . '/*');
    }
}
