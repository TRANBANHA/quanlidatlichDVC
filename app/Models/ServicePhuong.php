<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePhuong extends Model
{
    use HasFactory;

    protected $table = 'dich_vu_phuong';

    protected $fillable = [
        'dich_vu_id',
        'don_vi_id',
        'thoi_gian_xu_ly',
        'so_luong_toi_da',
        'phi_dich_vu',
        'kich_hoat',
        'ghi_chu',
    ];

    protected $casts = [
        'kich_hoat' => 'boolean',
        'phi_dich_vu' => 'decimal:2',
        'thoi_gian_xu_ly' => 'integer',
        'so_luong_toi_da' => 'integer',
    ];

    public function dichVu()
    {
        return $this->belongsTo(Service::class, 'dich_vu_id');
    }

    public function donVi()
    {
        return $this->belongsTo(DonVi::class, 'don_vi_id');
    }
}
