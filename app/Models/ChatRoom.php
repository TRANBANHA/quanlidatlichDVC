<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $table = 'phong_chat';

    protected $fillable = [
        'ma_phong', 'nguoi_dung_id', 'quan_tri_id', 'don_vi_id', 'ten_nguoi_dung', 
        'email_nguoi_dung', 'so_dien_thoai', 'trang_thai', 'hoat_dong_cuoi', 'use_rasa'
    ];

    protected $casts = [
        'hoat_dong_cuoi' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'quan_tri_id');
    }

    public function donVi()
    {
        return $this->belongsTo(DonVi::class, 'don_vi_id');
    }

    /**
     * Kiểm tra có cán bộ đang active trong room không
     */
    public function hasActiveAdmin()
    {
        return $this->quan_tri_id && $this->trang_thai === 'active';
    }

    /**
     * Kiểm tra có đang dùng Rasa không
     */
    public function isUsingRasa()
    {
        return $this->use_rasa && !$this->hasActiveAdmin();
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'phong_chat_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'phong_chat_id')->latest();
    }

    // Tạo room ID ngẫu nhiên
    public static function generateRoomId()
    {
        return 'room_' . uniqid() . '_' . time();
    }
}