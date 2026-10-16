<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LopHocResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ma_lop' => $this->ma_lop,
            'ten_lop' => $this->ten_lop,
            'giang_vien_id' => $this->giang_vien_id,
            'giao_vien' => $this->giao_vien,
            'so_dien_thoai_gvcn' => $this->so_dien_thoai_gvcn,
            'si_so' => $this->si_so,
            'ghi_chu' => $this->ghi_chu,
            'trang_thai' => (bool) $this->trang_thai,
            // Chỉ có khi controller gọi withCount('sinhViens')
            'so_sinh_vien' => $this->whenCounted('sinhViens'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
