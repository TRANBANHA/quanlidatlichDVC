<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'lien_he';

    protected $fillable = [
        'ten',
        'email',
        'so_dien_thoai',
        'chu_de',
        'tin_nhan',
        'trang_thai',
        'phan_hoi',
        'ngay_phan_hoi',
    ];

    protected $casts = [
        'ngay_phan_hoi' => 'datetime',
    ];

    /**
     * Scope a query to only include new contacts.
     */
    public function scopeNew($query)
    {
        return $query->where('trang_thai', 'new');
    }

    /**
     * Scope a query to only include read contacts.
     */
    public function scopeRead($query)
    {
        return $query->where('trang_thai', 'read');
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update(['trang_thai' => 'read']);
    }

    /**
     * Mark as replied
     */
    public function markAsReplied($reply = null)
    {
        $this->update([
            'trang_thai' => 'replied',
            'phan_hoi' => $reply,
            'ngay_phan_hoi' => now(),
        ]);
    }
}

