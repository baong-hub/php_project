<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LopHoc extends Model
{
    use HasFactory;
    // protected $table = 'lop_hocs';
    // được phép
    // tự động bỏ các trường không có trong $fillable khi insert vào database
    protected $fillable = [
        'ten_lop',
        'ma_lop',
        'giang_vien_id',
        'giao_vien',
        'so_dien_thoai_gvcn',
        'ghi_chu',
        'si_so',
        'trang_thai',
    ];

    protected static function booted(): void
    {
        // giao_vien là tên hiển thị, luôn lấy theo tài khoản giảng viên chủ nhiệm
        static::saving(function (LopHoc $lopHoc) {
            if ($lopHoc->giang_vien_id && $lopHoc->isDirty('giang_vien_id')) {
                $lopHoc->giao_vien = User::whereKey($lopHoc->giang_vien_id)->value('name');
            }
        });
    }

    /**
     * Lớp học có nhiều sinh viên
     */
    public function sinhViens()
    {
        return $this->hasMany(SinhVien::class, 'lop_hoc_id');
    }

    /**
     * Giảng viên chủ nhiệm lớp
     */
    public function giangVien()
    {
        return $this->belongsTo(User::class, 'giang_vien_id');
    }

    /**
     * Các lớp người dùng được quản lý: admin thấy tất cả,
     * giảng viên chỉ thấy lớp mình chủ nhiệm.
     */
    public function scopeCuaNguoiDung(Builder $query, User $user): Builder
    {
        return $query->when(!$user->isAdmin(), fn ($q) => $q->where('giang_vien_id', $user->id));
    }
}
