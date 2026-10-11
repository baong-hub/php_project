<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // Ô lọc để trống được Laravel đổi thành null (ConvertEmptyStringsToNull),
        // nên dùng filled() thay vì so sánh với '' để không lọc nhầm theo null.
        $perPage   = (int) $request->input('per_page', 10);
        $search    = trim((string) $request->input('search', ''));
        $trangThai = (string) $request->input('trang_thai', '');
        $viTri     = (string) $request->input('vi_tri', '');
        $sortBy    = $request->input('sort_by', 'id');
        $sortDir   = $request->input('sort_dir', 'asc');

        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $allowedSorts = ['id', 'ten', 'url', 'vi_tri', 'thu_tu', 'trang_thai'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }
        $sortDir = $sortDir === 'desc' ? 'desc' : 'asc';

        $query = Menu::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ten', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhere('nhom', 'like', "%{$search}%");
            });
        }

        if (in_array($trangThai, ['0', '1'], true)) {
            $query->where('trang_thai', $trangThai === '1');
        }

        if (array_key_exists($viTri, Menu::VI_TRI)) {
            $query->where('vi_tri', $viTri);
        }

        $menus = $query->orderBy($sortBy, $sortDir)
                       ->paginate($perPage)
                       ->withQueryString();

        return view('menu.index', [
            'title' => 'Danh sách Menu',
            'menus' => $menus,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
            'perPage' => $perPage,
            'viTriOptions' => Menu::VI_TRI,
        ]);
    }

    public function create()
    {
        return view('menu.create', [
            'title'        => 'Thêm Menu',
            'viTriOptions' => Menu::VI_TRI,
            'iconOptions'  => Menu::ICONS,
        ]);
    }

    public function store(StoreMenuRequest $request)
    {
        try {
            Menu::create($request->validated());

            return redirect()->route('menu.index')
                ->with('success', 'Thêm menu thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Lỗi khi thêm menu: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        return view('menu.edit', [
            'title'        => 'Sửa Menu',
            'menu'         => $menu,
            'viTriOptions' => Menu::VI_TRI,
            'iconOptions'  => Menu::ICONS,
        ]);
    }

    public function update(UpdateMenuRequest $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $menu->update($request->validated());

        return redirect()->route('menu.index')
            ->with('success', 'Cập nhật menu thành công!');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return redirect()->route('menu.index')
            ->with('success', 'Đã xóa menu thành công!');
    }
}