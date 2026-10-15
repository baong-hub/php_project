<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class SinhVienResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ma_sv' => $this->ma_sv,
            'ho_ten' => $this->ho_ten,
            'email' => $this->email,
            'ngay_sinh' => $this->ngay_sinh ? Carbon::parse($this->ngay_sinh)->toDateString() : null,
            'gioi_tinh' => (bool) $this->gioi_tinh,
            'gioi_tinh_text' => $this->gioi_tinh ? 'Nam' : 'Nữ',
            'so_dien_thoai' => $this->so_dien_thoai,
            'dia_chi' => $this->dia_chi,
            'trang_thai' => (bool) $this->trang_thai,
            'lop_hoc_id' => $this->lop_hoc_id,
            'lop_hoc' => new LopHocResource($this->whenLoaded('lopHoc')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
