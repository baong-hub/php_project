<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLopHocRequest;
use App\Http\Requests\UpdateLopHocRequest;
use App\Http\Resources\LopHocResource;
use App\Models\LopHoc;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LopHocController extends Controller
{
    use ApiResponse;

    private const SORTABLE = ['id', 'ma_lop', 'ten_lop', 'giao_vien', 'si_so', 'trang_thai', 'created_at'];

    /**
     * GET /api/lop-hoc?search=&trang_thai=&sort_by=&sort_dir=&per_page=&page=
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search'     => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', Rule::in(['0', '1'])],
            'sort_by'    => ['nullable', Rule::in(self::SORTABLE)],
            'sort_dir'   => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page'   => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $search = trim($filters['search'] ?? '');

        $lopHocs = LopHoc::query()
            ->withCount('sinhViens')
            ->cuaNguoiDung($request->user())
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('ma_lop', 'like', "%{$search}%")
                      ->orWhere('ten_lop', 'like', "%{$search}%")
                      ->orWhere('giao_vien', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['trang_thai']), fn ($q) => $q->where('trang_thai', $filters['trang_thai'] === '1'))
            ->orderBy($filters['sort_by'] ?? 'id', $filters['sort_dir'] ?? 'asc')
            ->orderBy('id')
            ->paginate((int) ($filters['per_page'] ?? 10))
            ->withQueryString();

        return $this->paginated($lopHocs, LopHocResource::class);
    }

    /**
     * POST /api/lop-hoc
     */
    public function store(StoreLopHocRequest $request)
    {
        $lopHoc = LopHoc::create($request->validated());

        return $this->success(new LopHocResource($lopHoc), 'Thêm lớp học thành công', 201);
    }

    /**
     * GET /api/lop-hoc/{id}
     */
    public function show(Request $request, $id)
    {
        $lopHoc = LopHoc::withCount('sinhViens')->cuaNguoiDung($request->user())->findOrFail($id);

        return $this->success(new LopHocResource($lopHoc));
    }

    /**
     * PUT /api/lop-hoc/{id}
     */
    public function update(UpdateLopHocRequest $request, $id)
    {
        $lopHoc = LopHoc::cuaNguoiDung($request->user())->findOrFail($id);
        $lopHoc->update($request->validated());

        return $this->success(new LopHocResource($lopHoc), 'Cập nhật lớp học thành công');
    }

    /**
     * DELETE /api/lop-hoc/{id}
     */
    public function destroy(Request $request, $id)
    {
        LopHoc::cuaNguoiDung($request->user())->findOrFail($id)->delete();

        return $this->success(null, 'Đã xóa lớp học');
    }
}
