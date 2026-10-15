<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSinhVienRequest;
use App\Http\Requests\UpdateSinhVienRequest;
use App\Http\Resources\SinhVienResource;
use App\Models\SinhVien;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SinhVienController extends Controller
{
    use ApiResponse;

    private const SORTABLE = ['id', 'ma_sv', 'ho_ten', 'email', 'ngay_sinh', 'gioi_tinh', 'trang_thai', 'created_at'];

    /**
     * GET /api/sinh-vien?search=&lop_hoc_id=&gioi_tinh=&trang_thai=&sort_by=&sort_dir=&per_page=&page=
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search'     => ['nullable', 'string', 'max:100'],
            'lop_hoc_id' => ['nullable', 'integer', 'exists:lop_hocs,id'],
            'gioi_tinh'  => ['nullable', Rule::in(['0', '1'])],
            'trang_thai' => ['nullable', Rule::in(['0', '1'])],
            'sort_by'    => ['nullable', Rule::in(self::SORTABLE)],
            'sort_dir'   => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page'   => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $search = trim($filters['search'] ?? '');

        $sinhViens = SinhVien::query()
            ->with('lopHoc')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('ma_sv', 'like', "%{$search}%")
                      ->orWhere('ho_ten', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('so_dien_thoai', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['lop_hoc_id']), fn ($q) => $q->where('lop_hoc_id', $filters['lop_hoc_id']))
            ->when(isset($filters['gioi_tinh']), fn ($q) => $q->where('gioi_tinh', $filters['gioi_tinh'] === '1'))
            ->when(isset($filters['trang_thai']), fn ($q) => $q->where('trang_thai', $filters['trang_thai'] === '1'))
            ->orderBy($filters['sort_by'] ?? 'id', $filters['sort_dir'] ?? 'asc')
            ->orderBy('id')
            ->paginate((int) ($filters['per_page'] ?? 10))
            ->withQueryString();

        return $this->paginated($sinhViens, SinhVienResource::class);
    }

    /**
     * POST /api/sinh-vien
     */
    public function store(StoreSinhVienRequest $request)
    {
        $sinhVien = SinhVien::create($request->validated())->load('lopHoc');

        return $this->success(new SinhVienResource($sinhVien), 'Thêm sinh viên thành công', 201);
    }

    /**
     * GET /api/sinh-vien/{id}
     */
    public function show($id)
    {
        $sinhVien = SinhVien::with('lopHoc')->findOrFail($id);

        return $this->success(new SinhVienResource($sinhVien));
    }

    /**
     * PUT /api/sinh-vien/{id}
     */
    public function update(UpdateSinhVienRequest $request, $id)
    {
        $sinhVien = SinhVien::findOrFail($id);
        $sinhVien->update($request->validated());

        return $this->success(new SinhVienResource($sinhVien->load('lopHoc')), 'Cập nhật sinh viên thành công');
    }

    /**
     * DELETE /api/sinh-vien/{id}
     */
    public function destroy($id)
    {
        SinhVien::findOrFail($id)->delete();

        return $this->success(null, 'Đã xóa sinh viên');
    }
}
