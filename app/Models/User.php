<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_GIANG_VIEN = 'giang_vien';

    // Các nhóm người dùng: khóa => tên hiển thị
    public const ROLES = [
        self::ROLE_ADMIN => 'Quản trị viên',
        self::ROLE_GIANG_VIEN => 'Giảng viên',
    ];

    // Laravel 11 không hỗ trợ attribute #[Fillable]/#[Hidden], phải khai báo bằng thuộc tính
    protected $fillable = [
        'name',
        'email',
        'role',
        'trang_thai',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'trang_thai' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Đổi tên giảng viên thì cập nhật luôn tên hiển thị ở các lớp chủ nhiệm
        static::updated(function (User $user) {
            if ($user->wasChanged('name')) {
                LopHoc::where('giang_vien_id', $user->id)->update(['giao_vien' => $user->name]);
            }
        });
    }

    /**
     * Các lớp giảng viên này chủ nhiệm
     */
    public function lopChuNhiem()
    {
        return $this->hasMany(LopHoc::class, 'giang_vien_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function tenVaiTro(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    /**
     * Chữ viết tắt hiển thị trong avatar (vd: "Nguyễn Văn An" => "NA").
     */
    public function vietTat(): string
    {
        $words = preg_split('/\s+/u', trim($this->name)) ?: [];
        $first = mb_substr($words[0] ?? '', 0, 1);
        $last = count($words) > 1 ? mb_substr(end($words), 0, 1) : '';

        return mb_strtoupper($first . $last);
    }
}
