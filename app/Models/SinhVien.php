<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SinhVien extends Model
{
    use HasFactory;

    protected $table = 'sinh_viens';

    protected $fillable = [
        'ma_sv',
        'ho_ten',
        'email',
        'ngay_sinh',
        'gioi_tinh',
        'lop_hoc_id',
        'so_dien_thoai',
        'dia_chi',
        'trang_thai',
    ];

    /**
     * Sinh viên thuộc về 1 lớp học
     */
    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    /**
     * Các sinh viên người dùng được quản lý: admin thấy tất cả,
     * giảng viên chỉ thấy sinh viên thuộc các lớp mình chủ nhiệm.
     */
    public function scopeCuaNguoiDung(Builder $query, User $user): Builder
    {
        return $query->when(!$user->isAdmin(), fn ($q) => $q->whereIn(
            'sinh_viens.lop_hoc_id',
            LopHoc::select('id')->where('giang_vien_id', $user->id)
        ));
    }
}
