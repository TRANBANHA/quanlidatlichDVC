<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Khai báo bảng tương ứng trong cơ sở dữ liệu
    protected $table = 'nguoi_dung';

    // Khai báo các cột có thể gán giá trị mass assignment
    protected $fillable = [
        'ten',
        'email',
        'so_dien_thoai',
        'cccd',
        'mat_khau',
        'tinh_trang',
        'don_vi_id',
        'dia_chi',
        'code',
        'loai_phuong',
    ];
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    public function hoSos()
    {
        return $this->hasMany(HoSo::class, 'nguoi_dung_id');
    }

    /**
     * Quan hệ: Người dùng thuộc về một đơn vị/phường
     */
    public function donVi()
    {
        return $this->belongsTo(DonVi::class, 'don_vi_id');
    }
}