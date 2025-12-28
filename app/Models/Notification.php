<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Khai báo bảng tương ứng trong cơ sở dữ liệu
    protected $table = 'thong_bao_admin';

    // Khai báo các cột có thể gán giá trị mass assignment
    protected $fillable = [
        'tieu_de',
        'noi_dung',
        'ngay_dang',
        'ngay_het_han',
        'hinh_anh',
        'video',
        'nguoi_dung_id',
        'type',
    ];

    // Mối quan hệ với bảng `nguoi_dung`
    public function user()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    // Accessors để tương thích với code cũ
    public function getTitleAttribute()
    {
        return $this->attributes['tieu_de'] ?? null;
    }

    public function getContentAttribute()
    {
        return $this->attributes['noi_dung'] ?? null;
    }

    public function getPublishDateAttribute()
    {
        return $this->attributes['ngay_dang'] ?? null;
    }

    public function getExpiryDateAttribute()
    {
        return $this->attributes['ngay_het_han'] ?? null;
    }

    public function getImageAttribute()
    {
        return $this->attributes['hinh_anh'] ?? null;
    }

    public function getUserIdAttribute()
    {
        return $this->attributes['nguoi_dung_id'] ?? null;
    }

    // Mutators để tương thích với code cũ
    public function setTitleAttribute($value)
    {
        $this->attributes['tieu_de'] = $value;
    }

    public function setContentAttribute($value)
    {
        $this->attributes['noi_dung'] = $value;
    }

    public function setPublishDateAttribute($value)
    {
        $this->attributes['ngay_dang'] = $value;
    }

    public function setExpiryDateAttribute($value)
    {
        $this->attributes['ngay_het_han'] = $value;
    }

    public function setImageAttribute($value)
    {
        $this->attributes['hinh_anh'] = $value;
    }

    public function setUserIdAttribute($value)
    {
        $this->attributes['nguoi_dung_id'] = $value;
    }
}
