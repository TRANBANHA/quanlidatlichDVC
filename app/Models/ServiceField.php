<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceField extends Model
{
    use HasFactory;

    protected $table = 'dich_vu_truong_du_lieu';

    protected $fillable = [
        'dich_vu_id',
        'ten_truong',
        'nhan_hien_thi',
        'loai_truong',
        'bat_buoc',
        'tuy_chon',
        'placeholder',
        'goi_y',
        'thu_tu',
    ];

    protected $casts = [
        'bat_buoc' => 'boolean',
        'tuy_chon' => 'array',
        'thu_tu' => 'integer',
    ];

    public function dichVu()
    {
        return $this->belongsTo(Service::class, 'dich_vu_id');
    }
}
