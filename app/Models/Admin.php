<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Admin extends Authenticatable
{
    use HasFactory;
    protected $table = "quan_tri_vien";
    
    // Các loại quyền
    public const ADMIN = 1;           // Admin tổng (quản lý toàn hệ thống)
    public const ADMIN_PHUONG = 2;   // Admin phường (quản lý phường của mình)
    public const CAN_BO = 0;         // Cán bộ phường (xử lý hồ sơ)
    
    public static function getRoleName($quyen)
    {
        return match($quyen) {
            self::ADMIN => 'Admin tổng',
            self::ADMIN_PHUONG => 'Admin phường',
            self::CAN_BO => 'Cán bộ phường',
            default => 'Không xác định',
        };
    }
    
    public function isAdmin()
    {
        return $this->quyen == self::ADMIN;
    }
    
    public function isAdminPhuong()
    {
        return $this->quyen == self::ADMIN_PHUONG;
    }
    
    public function isCanBo()
    {
        return $this->quyen == self::CAN_BO;
    }
    protected $fillable = [
        'ho_ten',
        'ten_dang_nhap',
        'mat_khau',
        'quyen',
        'email',
        'so_dien_thoai',
        'don_vi_id',
        'hinh_anh',
    ];

    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    public function donVi()
    {
        return $this->belongsTo(DonVi::class, 'don_vi_id');
    }

    // Một cán bộ có nhiều hồ sơ
    public function hoSo()
    {
        return $this->hasMany(HoSo::class, 'quan_tri_vien_id');
    }
}
