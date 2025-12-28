<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use HasFactory;

    protected $table = 'gioi_thieu';

    protected $fillable = [
        'tieu_de',
        'noi_dung',
        'hinh_anh',
        'su_menh',
        'tam_nhin',
        'gia_tri',
        'so_dien_thoai',
        'email',
        'dia_chi',
    ];

    /**
     * Get the first about record (singleton pattern)
     */
    public static function getFirst()
    {
        return static::first() ?? new static();
    }
}

