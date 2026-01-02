<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CanBoNghi extends Model
{
    use HasFactory;

    protected $table = 'can_bo_nghi';

    // Các trạng thái báo nghỉ
    public const TRANG_THAI_CHO_DUYET = 'cho_duyet';
    public const TRANG_THAI_DA_DUYET = 'da_duyet';
    public const TRANG_THAI_TU_CHOI = 'tu_choi';

    public const TRANG_THAI_OPTIONS = [
        self::TRANG_THAI_CHO_DUYET => 'Chờ duyệt',
        self::TRANG_THAI_DA_DUYET => 'Đã duyệt',
        self::TRANG_THAI_TU_CHOI => 'Từ chối',
    ];

    protected $fillable = [
        'can_bo_id',
        'ngay_nghi',
        'ly_do',
        'da_chuyen_ho_so',
        'trang_thai',
        'nguoi_duyet_id',
        'ngay_duyet',
        'ghi_chu_duyet',
    ];

    protected $casts = [
        'ngay_nghi' => 'date',
        'da_chuyen_ho_so' => 'boolean',
        'ngay_duyet' => 'datetime',
    ];

    /**
     * Quan hệ: Một bản ghi nghỉ thuộc về một cán bộ
     */
    public function canBo()
    {
        return $this->belongsTo(Admin::class, 'can_bo_id');
    }

    /**
     * Quan hệ: Người duyệt
     */
    public function nguoiDuyet()
    {
        return $this->belongsTo(Admin::class, 'nguoi_duyet_id');
    }

    /**
     * Kiểm tra đã được duyệt chưa
     */
    public function isDaDuyet()
    {
        return $this->trang_thai === self::TRANG_THAI_DA_DUYET;
    }

    /**
     * Kiểm tra đang chờ duyệt
     */
    public function isChoDuyet()
    {
        return $this->trang_thai === self::TRANG_THAI_CHO_DUYET;
    }

    /**
     * Kiểm tra bị từ chối
     */
    public function isTuChoi()
    {
        return $this->trang_thai === self::TRANG_THAI_TU_CHOI;
    }

    /**
     * Scope: Lấy các cán bộ nghỉ trong ngày
     */
    public function scopeNghiTrongNgay($query, $ngay)
    {
        return $query->whereDate('ngay_nghi', $ngay);
    }

    /**
     * Scope: Lấy các cán bộ nghỉ trong khoảng thời gian
     */
    public function scopeNghiTrongKhoang($query, $tuNgay, $denNgay)
    {
        return $query->whereBetween('ngay_nghi', [$tuNgay, $denNgay]);
    }

    /**
     * Kiểm tra cán bộ có nghỉ trong ngày không
     */
    public static function canBoNghiTrongNgay($canBoId, $ngay)
    {
        return self::where('can_bo_id', $canBoId)
            ->whereDate('ngay_nghi', $ngay)
            ->exists();
    }

    /**
     * Lấy danh sách ID cán bộ nghỉ trong ngày (chỉ lấy những người đã được duyệt)
     */
    public static function danhSachCanBoNghiTrongNgay($ngay, $donViId = null)
    {
        $query = self::whereDate('ngay_nghi', $ngay)
            ->where('trang_thai', self::TRANG_THAI_DA_DUYET); // Chỉ lấy những người đã được duyệt
        
        if ($donViId) {
            $query->whereHas('canBo', function($q) use ($donViId) {
                $q->where('don_vi_id', $donViId);
            });
        }
        
        return $query->pluck('can_bo_id')->toArray();
    }
}
