<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoSoField extends Model
{
    use HasFactory;

    protected $table = 'ho_so_truong_du_lieu';

    protected $fillable = [
        'ho_so_id',
        'ten_truong',
        'gia_tri',
    ];

    public function hoSo()
    {
        return $this->belongsTo(HoSo::class, 'ho_so_id');
    }
}
