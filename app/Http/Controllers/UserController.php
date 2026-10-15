<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private const PER_PAGE_OPTIONS = [10, 25, 50];
    private const SORTABLE = ['id', 'name', 'email', 'role', 'trang_thai', 'created_at'];

    public function index(Request $request)
    {
        // Tham số không hợp lệ thì dùng giá trị mặc định thay vì báo lỗi
        $perPage   = (int) $request->input('per_page', 10);
        $search    = trim((string) $request->input('search', ''));
        $role      = (string) $request->input('role', '');
        $trangThai = (string) $request->input('trang_thai', '');
        $sortBy    = $request->input('sort_by', 'id');
        $sortDir   = $request->input('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';

        if (!in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = 10;
        }
        if (!in_array($sortBy, self::SORTABLE, true)) {
            $sortBy = 'id';
        }

        $users = User::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(array_key_exists($role, User::ROLES), fn ($q) => $q->where('role', $role))
            ->when(in_array($trangThai, ['0', '1'], true), fn ($q) => $q->where('trang_thai', $trangThai === '1'))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('nguoidung.index', [
            'title'       => 'Quản lý người dùng',
            'users'       => $users,
            'roleOptions' => User::ROLES,
            'perPage'     => $perPage,
            'sortBy'      => $sortBy,
            'sortDir'     => $sortDir,
        ]);
    }

    public function create()
    {
        return view('nguoidung.create', [
            'title'       => 'Thêm người dùng',
            'roleOptions' => User::ROLES,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        User::create($request->validated());

        return redirect()->route('nguoidung.index')
            ->with('success', 'Thêm người dùng thành công!');
    }

    public function edit($id)
    {
        return view('nguoidung.edit', [
            'title'       => 'Sửa người dùng',
            'user'        => User::findOrFail($id),
            'roleOptions' => User::ROLES,
        ]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        // Để trống mật khẩu = giữ nguyên
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('nguoidung.index')
            ->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return redirect()->route('nguoidung.index')
                ->with('error', 'Bạn không thể xóa tài khoản đang đăng nhập.');
        }

        $user->tokens()->delete();
        $user->delete();

        return redirect()->route('nguoidung.index')
            ->with('success', 'Đã xóa người dùng thành công!');
    }
}
