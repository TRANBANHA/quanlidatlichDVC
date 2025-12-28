<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HoSo extends Model
{
    use HasFactory;

    protected $table = 'ho_so';

    public const STATUS_RECEIVED = 'Đã tiếp nhận';
    public const STATUS_PROCESSING = 'Đang xử lý';
    public const STATUS_NEED_SUPPLEMENT = 'Cần bổ sung hồ sơ';
    public const STATUS_COMPLETED = 'Hoàn tất';
    public const STATUS_CANCELLED = 'Đã hủy';

    public const STATUS_OPTIONS = [
        self::STATUS_RECEIVED,
        self::STATUS_PROCESSING,
        self::STATUS_NEED_SUPPLEMENT,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'ma_ho_so',
        'dich_vu_id',
        'nguoi_dung_id',
        'don_vi_id',
        'gio_hen',
        'ngay_hen',
        'so_thu_tu',
        'ghi_chu',
        'trang_thai',
        'file_path', // thêm nếu có cột file trong DB
        'quan_tri_vien_id',
        'cancelled_at',
        'ly_do_huy',
    ];

    protected $casts = [
        'ngay_hen' => 'date',
        'cancelled_at' => 'datetime',
    ];

    // Một hồ sơ thuộc về một dịch vụ
    public function dichVu()
    {
        return $this->belongsTo(Service::class, 'dich_vu_id');
    }

    // Một hồ sơ thuộc về một người dùng
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
    // Một hồ sơ thuộc về một người dùng
    public function quanTriVien()
    {
        return $this->belongsTo(Admin::class, 'quan_tri_vien_id');
    }

    // Một hồ sơ thuộc về một đơn vị
    public function donVi()
    {
        return $this->belongsTo(DonVi::class, 'don_vi_id');
    }
    // Một hồ sơ thuộc về một đơn vị
    public function thongBao()
    {
        return $this->hasOne(ThongBao::class, 'ho_so_id');
    }

    // Một hồ sơ có một đánh giá
    public function rating()
    {
        return $this->hasOne(Rating::class, 'ho_so_id');
    }

    // Một hồ sơ có nhiều fields động
    public function hoSoFields()
    {
        return $this->hasMany(HoSoField::class, 'ho_so_id');
    }

    public function scopeActive($query)
    {
        return $query->where('trang_thai', '!=', self::STATUS_CANCELLED);
    }

    public function isCompleted(): bool
    {
        return $this->trang_thai === self::STATUS_COMPLETED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->trang_thai, [
            self::STATUS_RECEIVED,
            self::STATUS_PROCESSING,
            self::STATUS_NEED_SUPPLEMENT,
        ], true);
    }

    public function canBeEdited(): bool
    {
        return in_array($this->trang_thai, [
            self::STATUS_RECEIVED,
            self::STATUS_PROCESSING,
            self::STATUS_NEED_SUPPLEMENT,
        ], true);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'HS' . now()->format('Ymd') . Str::upper(Str::random(4));
        } while (self::where('ma_ho_so', $code)->exists());

        return $code;
    }
}
