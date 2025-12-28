<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
class ServiceSchedule extends Authenticatable
{
    use HasFactory;
    protected $table = "lich_dich_vu";
    protected $fillable = [
        'dich_vu_id',
        'thu_trong_tuan',
        'trang_thai',
        'gio_bat_dau',
        'gio_ket_thuc',
        'so_luong_toi_da',
        'so_luong_hien_tai',
        'ghi_chu',
        'file_dinh_kem'
    ];
    public function service()
    {
        return $this->belongsTo(Service::class, 'dich_vu_id');
    }


}
