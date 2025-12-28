<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'tin_nhan';

    protected $fillable = [
        'phong_chat_id',
        'nguoi_gui_id',
        'loai_nguoi_gui',
        'ten_nguoi_gui',
        'tin_nhan',
        'loai_tin_nhan',
        'da_doc'
    ];

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'phong_chat_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'nguoi_gui_id');
    }
}