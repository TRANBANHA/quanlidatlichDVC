<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAssignment extends Model
{
    use HasFactory;

    // Tên bảng trong DB
    protected $table = 'phan_cong_dich_vu';

    // Các cột cho phép gán hàng loạt
    protected $fillable = [
        'ma_can_bo',
        'ma_dich_vu',
        'ngay_phan_cong',
        'ghi_chu',
    ];

    // Nếu bạn dùng JSON để lưu danh sách mã cán bộ
    protected $casts = [
        'ma_can_bo' => 'array',
    ];

    // Quan hệ: Mỗi phân công thuộc về 1 dịch vụ
    public function dichVu()
    {
        return $this->belongsTo(Service::class, 'ma_dich_vu', 'id');
    }

}
