<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'ho_so_id',
        'nguoi_dung_id',
        'quan_tri_vien_id',
        'diem',
        'binh_luan',
        'diem_thai_do',
        'diem_thoi_gian',
        'diem_chat_luong',
        'diem_co_so_vat_chat',
        'co_nen_gioi_thieu',
        'y_kien_khac',
    ];

    // Quan hệ với HoSo
    public function hoSo()
    {
        return $this->belongsTo(HoSo::class, 'ho_so_id');
    }

    // Quan hệ với User
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    // Quan hệ với Admin (Cán bộ được đánh giá)
    public function quanTriVien()
    {
        return $this->belongsTo(Admin::class, 'quan_tri_vien_id');
    }
}
