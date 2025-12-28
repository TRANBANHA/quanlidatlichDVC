<?php

namespace App\Models;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThongBao extends Model
{
    use HasFactory;

    protected $table = 'thong_bao';

    protected $fillable = [
        'ho_so_id',
        'nguoi_dung_id',
        'dich_vu_id',
        'ngay_hen',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'ngay_hen' => 'date',
    ];

    /**
     * Quan hệ: Một thông báo thuộc về một hồ sơ
     */
    public function hoSo()
    {
        return $this->belongsTo(HoSo::class, 'ho_so_id');
    }

    public function NguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    /**
     * Quan hệ: Một thông báo thuộc về một dịch vụ
     */
    public function dichVu()
    {
        return $this->belongsTo(Service::class, 'dich_vu_id');
    }
}
