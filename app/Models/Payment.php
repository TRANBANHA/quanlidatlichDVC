<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'thanh_toan';

    protected $fillable = [
        'nguoi_dung_id',
        'ho_so_id',
        'loai_hinh',
        'ma_ban_ghi',
        'so_tien',
        'trang_thai_thanh_toan',
        'hinh_anh',
        'giai_trinh',
        'ma_giao_dich',
        'phuong_thuc_thanh_toan',
        'du_lieu_vnpay',
        'ngay_thanh_toan',
    ];

    protected $casts = [
        'du_lieu_vnpay' => 'array',
        'ngay_thanh_toan' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function donVi()
    {
        return $this->belongsTo(DonVi::class, 'don_vi_id');
    }

    public function dichVu()
    {
        return $this->belongsTo(Service::class, 'dich_vu_id');
    }

    public function hoSo()
    {
        return $this->belongsTo(HoSo::class, 'ho_so_id');
    }
}
